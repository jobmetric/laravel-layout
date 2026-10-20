<?php

namespace JobMetric\Layout;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use JobMetric\Layout\Events\HasLayout\LayoutRelationDeletedEvent;
use JobMetric\Layout\Events\HasLayout\LayoutRelationStoredEvent;
use JobMetric\Layout\Exceptions\ApplicationNotRegisteredException;
use JobMetric\Layout\Exceptions\CollectionPropertyNotExistException;
use JobMetric\Layout\Facades\ApplicationRegistry;
use JobMetric\Layout\Models\Layout;
use JobMetric\Layout\Models\LayoutPage;
use JobMetric\Layout\Models\LayoutRelation;
use Throwable;

/**
 * Trait HasLayout
 *
 * Binds a model to layouts per application/collection via the layout_relations polymorphic table.
 * Mirrors HasTranslation design: optional model properties are read in initializeHasLayout() into base*.
 *
 * Model may define (optional):
 *   protected string $layoutPageType = 'product';      // page type for layout_pages fallback
 *   protected ?string $layoutCollectionField = null;   // attribute name for collection value
 *
 * Resolves layout from layout_relations first; falls back to layout_pages by application + page type;
 * then to config default_layouts per application.
 *
 * @package JobMetric\Layout
 *
 * @property-read Collection<int, LayoutRelation> $layoutRelatable
 *
 * @method static Builder|static hasLayout(string $application, ?string $collection = null)
 * @method static Builder|static hasLayoutForApplication(string $application)
 */
trait HasLayout
{
    /**
     * Page type used when resolving layout from layout_pages. Set from model property in initializeHasLayout().
     *
     * @var string
     */
    private string $baseLayoutPageType = 'default';

    /**
     * Attribute name on the model that holds the collection value; null if not used. Set from model property in
     * initializeHasLayout().
     *
     * @var string|null
     */
    private ?string $baseLayoutCollectionField = null;

    /**
     * Import model-level layout config into base* (mirrors HasTranslation initialize).
     *
     * @return void
     * @throws Throwable
     */
    public function initializeHasLayout(): void
    {
        if (function_exists('hasPropertyInClass')) {
            if (hasPropertyInClass($this, 'layoutPageType')) {
                $val = $this->layoutPageType ?? null;
                $this->baseLayoutPageType = is_string($val) && $val !== '' ? $val : Str::snake(class_basename($this));
            }
            else {
                $this->baseLayoutPageType = Str::snake(class_basename($this));
            }
            if (hasPropertyInClass($this, 'layoutCollectionField')) {
                $val = $this->layoutCollectionField ?? null;
                $this->baseLayoutCollectionField = is_string($val) ? $val : null;
            }
            else {
                $this->baseLayoutCollectionField = null;
            }
        }
        else {
            if (property_exists($this, 'layoutPageType')) {
                $val = $this->layoutPageType ?? null;
                $this->baseLayoutPageType = is_string($val) && $val !== '' ? $val : Str::snake(class_basename($this));
            }
            else {
                $this->baseLayoutPageType = Str::snake(class_basename($this));
            }
            if (property_exists($this, 'layoutCollectionField')) {
                $val = $this->layoutCollectionField ?? null;
                $this->baseLayoutCollectionField = is_string($val) ? $val : null;
            }
            else {
                $this->baseLayoutCollectionField = null;
            }
        }
    }

    /**
     * Page type used when resolving layout from layout_pages (application + page). Reads from base*.
     *
     * @return string
     */
    protected function layoutPageType(): string
    {
        return $this->baseLayoutPageType;
    }

    /**
     * Attribute name on the model that holds the collection value; null if not used. Reads from base*.
     *
     * @return string|null
     */
    protected function layoutCollectionField(): ?string
    {
        return $this->baseLayoutCollectionField;
    }

    /**
     * Cache key for getLayout() result. Null application/collection normalized to empty string.
     *
     * @param string|null $application
     * @param string|null $collection
     *
     * @return string
     */
    protected function layoutCacheKey(?string $application, ?string $collection): string
    {
        return layout_relatable_cache_key(get_class($this), $this->getKey(), $application, $collection);
    }

    /**
     * Forget cached layout for this model and given application/collection.
     *
     * @param string|null $application
     * @param string|null $collection
     *
     * @return void
     */
    protected function layoutForgetCache(?string $application, ?string $collection): void
    {
        Cache::forget($this->layoutCacheKey($application, $collection));
    }

    /**
     * Polymorphic relation: layout_relations for this model (application + collection scoped).
     *
     * @return MorphMany<LayoutRelation>
     */
    public function layoutRelatable(): MorphMany
    {
        return $this->morphMany(LayoutRelation::class, 'relatable');
    }

    /**
     * Scope: models that have a layout relation for the given application and optional collection.
     *
     * @param Builder $query
     * @param string $application
     * @param string|null $collection
     *
     * @return Builder
     */
    public function scopeHasLayout(Builder $query, string $application, ?string $collection = null): Builder
    {
        return $query->whereHas('layoutRelatable', function (Builder $q) use ($application, $collection) {
            $q->where('application', $application);
            if ($collection !== null) {
                $q->where('collection', $collection);
            }
        });
    }

    /**
     * Scope: models that have at least one layout relation for the given application.
     *
     * @param Builder $query
     * @param string $application
     *
     * @return Builder
     */
    public function scopeHasLayoutForApplication(Builder $query, string $application): Builder
    {
        return $query->whereHas('layoutRelatable', fn (Builder $q) => $q->where('application', $application));
    }

    /**
     * Scope layoutRelatable by application.
     *
     * @param string $application
     *
     * @return MorphMany<LayoutRelation>
     */
    public function layoutApplicationTo(string $application): MorphMany
    {
        return $this->layoutRelatable()->where('application', $application);
    }

    /**
     * Scope layoutRelatable by collection.
     *
     * @param string $collection
     *
     * @return MorphMany<LayoutRelation>
     */
    public function layoutCollectionTo(string $collection): MorphMany
    {
        return $this->layoutRelatable()->where('collection', $collection);
    }

    /**
     * Scope layoutRelatable by application and optional collection.
     *
     * @param string $application
     * @param string|null $collection
     *
     * @return MorphMany<LayoutRelation>
     */
    public function layoutApplicationCollectionTo(string $application, ?string $collection = null): MorphMany
    {
        $query = $this->layoutRelatable()->where('application', $application);

        if ($collection !== null) {
            $query->where('collection', $collection);
        }

        return $query;
    }

    /**
     * Resolve collection value from model when layoutCollectionField() returns a non-null field name.
     *
     * @return string|null
     * @throws CollectionPropertyNotExistException
     */
    public function layoutGetCollectionValue(): ?string
    {
        $field = $this->layoutCollectionField();

        if ($field === null) {
            return null;
        }

        if (! array_key_exists($field, $this->getAttributes())) {
            throw new CollectionPropertyNotExistException(self::class, $field);
        }

        $value = $this->{$field};

        return $value === null ? null : (string) $value;
    }

    /**
     * Store a layout binding for this model for the given application and optional collection.
     * Validates application via ApplicationRegistry. Fires LayoutRelationStoredEvent and clears cache.
     *
     * @param Layout|int $layout
     * @param string $application
     * @param string|null $collection
     *
     * @return LayoutRelation
     * @throws ApplicationNotRegisteredException
     */
    public function storeLayout(Layout|int $layout, string $application, ?string $collection = null): LayoutRelation
    {
        if (! ApplicationRegistry::has($application)) {
            throw new ApplicationNotRegisteredException($application);
        }

        $layoutId = $layout instanceof Layout ? $layout->id : $layout;

        $relation = $this->layoutRelatable()->create([
            'application' => $application,
            'layout_id'   => $layoutId,
            'collection'  => $collection,
        ]);

        $this->layoutForgetCache($application, $collection);
        event(new LayoutRelationStoredEvent($relation, $this));
        if ($relation->exists) {
            app(\JobMetric\EventSystem\Support\CommittedEventDispatcher::class)->dispatch(new \JobMetric\Layout\Events\LayoutRelation\LayoutRelationStoreCommittedEvent($relation->getKey()), $relation->getConnectionName());
        }

        return $relation;
    }

    /**
     * Eager-load layoutRelatable (optionally filtered by application and/or collection).
     *
     * @param string|null $application
     * @param string|null $collection
     *
     * @return static
     */
    public function withLayoutRelatable(?string $application = null, ?string $collection = null): static
    {
        if ($application === null) {
            $this->load('layoutRelatable');

            return $this;
        }

        if ($collection === null) {
            $this->load(['layoutRelatable' => fn ($q) => $q->where('application', $application)]);

            return $this;
        }

        $this->load([
            'layoutRelatable' => fn ($q) => $q->where('application', $application)->where('collection', $collection),
        ]);

        return $this;
    }

    /**
     * Get the layout relation record for the given application and optional collection (no fallback).
     *
     * @param string|null $application
     * @param string|null $collection
     *
     * @return LayoutRelation|null
     * @throws CollectionPropertyNotExistException
     */
    public function getLayoutRelation(?string $application = null, ?string $collection = null): ?LayoutRelation
    {
        $collection = $collection ?? $this->layoutGetCollectionValue();

        return $this->layoutRelatable()
            ->when($application !== null, fn ($q) => $q->where('application', $application))
            ->when($collection !== null, fn ($q) => $q->where('collection', $collection))
            ->with('layout')
            ->first();
    }

    /**
     * Resolve the effective Layout for this model for the given application and optional collection.
     * Uses layout_relations first; falls back to layout_pages (application + layoutPageType());
     * then to ApplicationRegistry default_layout for that application. Result can be cached when config
     * layout.cache_time > 0 or null (forever).
     *
     * @param string|null $application
     * @param string|null $collection
     *
     * @return Layout|null
     * @throws CollectionPropertyNotExistException
     */
    public function getLayout(?string $application = null, ?string $collection = null): ?Layout
    {
        $collection = $collection ?? $this->layoutGetCollectionValue();

        $cacheTime = config('layout.cache_time', 0);

        $resolver = function () use ($application, $collection): ?Layout {
            $relation = $this->layoutRelatable()->when($application !== null, fn ($q
            ) => $q->where('application', $application))->when($collection !== null, fn ($q
            ) => $q->where('collection', $collection))->with('layout')->first();

            if ($relation !== null) {
                return $relation->layout;
            }

            if ($application === null) {
                return null;
            }

            $layoutPage = LayoutPage::query()
                ->applicationPage($application, $this->layoutPageType())
                ->with('layout')
                ->first();

            if ($layoutPage?->layout !== null) {
                return $layoutPage->layout;
            }

            $defaultId = ApplicationRegistry::getDefaultLayout($application);
            if ($defaultId !== null) {
                $layout = Layout::query()->find($defaultId);

                return $layout instanceof Layout ? $layout : null;
            }

            return null;
        };

        $key = $this->layoutCacheKey($application, $collection);

        if ($cacheTime === null) {
            return Cache::rememberForever($key, $resolver);
        }

        $ttlMinutes = (int) $cacheTime;
        if ($ttlMinutes > 0) {
            return Cache::remember($key, $ttlMinutes * 60, $resolver);
        }

        return $resolver();
    }

    /**
     * Whether this model has a layout (relation or effective) for the given application and optional collection.
     *
     * @param string|null $application
     * @param string|null $collection
     *
     * @return bool
     * @throws CollectionPropertyNotExistException
     */
    public function hasLayout(?string $application = null, ?string $collection = null): bool
    {
        return $this->getLayout($application, $collection) !== null;
    }

    /**
     * Get all effective layouts for the given application: from relations, then layout_pages fallback, then default.
     *
     * @param string $application
     *
     * @return Collection<int, Layout>
     * @throws CollectionPropertyNotExistException
     */
    public function getLayoutsForApplication(string $application): Collection
    {
        $collection = $this->layoutGetCollectionValue();
        $layouts = new Collection();

        $relations = $this->layoutRelatable()->where('application', $application)->when($collection !== null, fn ($q
        ) => $q->where('collection', $collection))->with('layout')->get();

        foreach ($relations as $rel) {
            if ($rel->layout !== null && ! $layouts->contains('id', $rel->layout->id)) {
                $layouts->push($rel->layout);
            }
        }

        $layoutPage = LayoutPage::query()
            ->applicationPage($application, $this->layoutPageType())
            ->with('layout')
            ->first();

        if ($layoutPage?->layout !== null && ! $layouts->contains('id', $layoutPage->layout->id)) {
            $layouts->push($layoutPage->layout);
        }

        $defaultId = ApplicationRegistry::getDefaultLayout($application);
        if ($defaultId !== null) {
            $layout = Layout::query()->find($defaultId);
            if ($layout instanceof Layout && ! $layouts->contains('id', $layout->id)) {
                $layouts->push($layout);
            }
        }

        return $layouts;
    }

    /**
     * Replace layout bindings for the given application (and optional collection) with the first layout from the list.
     * Validates application. Fires events and clears cache.
     *
     * @param string $application
     * @param array<int, Layout|int> $layoutIdsOrLayouts
     * @param string|null $collection
     *
     * @return static
     * @throws ApplicationNotRegisteredException
     */
    public function syncLayoutsForApplication(
        string $application,
        array $layoutIdsOrLayouts,
        ?string $collection = null
    ): static {
        $this->forgetLayout($application, $collection);

        $first = null;
        foreach ($layoutIdsOrLayouts as $item) {
            $first = $item;
            break;
        }

        if ($first !== null) {
            $this->storeLayout($first, $application, $collection);
        }

        return $this;
    }

    /**
     * Remove layout bindings for the given application and/or collection (null = all).
     * Fires LayoutRelationDeletedEvent and clears cache when scope is specific.
     *
     * @param string|null $application
     * @param string|null $collection
     *
     * @return static
     */
    public function forgetLayout(?string $application = null, ?string $collection = null): static
    {
        $query = $this->layoutRelatable()->when($application !== null, fn ($q
        ) => $q->where('application', $application))->when($collection !== null, fn ($q
        ) => $q->where('collection', $collection));

        $outcomeRelations = (clone $query)->get();
        $deletedCount = $query->count();
        $query->delete();

        if ($deletedCount > 0) {
            $this->layoutForgetCache($application, $collection);
            event(new LayoutRelationDeletedEvent($this, $application ?? '', $collection, $deletedCount));
            foreach ($outcomeRelations as $outcomeRelation) {
                if (! $outcomeRelation->newQuery()->whereKey($outcomeRelation->getKey())->exists()) {
                    app(\JobMetric\EventSystem\Support\CommittedEventDispatcher::class)->dispatch(new \JobMetric\Layout\Events\LayoutRelation\LayoutRelationDeleteCommittedEvent($outcomeRelation->getKey()), $outcomeRelation->getConnectionName());
                }
            }
        }

        return $this;
    }
}
