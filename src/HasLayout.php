<?php

namespace JobMetric\Layout;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use JobMetric\Layout\Exceptions\CollectionPropertyNotExistException;
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
 * Resolves layout from layout_relations first; falls back to layout_pages by application + page type.
 *
 * @package JobMetric\Layout
 *
 * @property-read Collection<int, LayoutRelation> $layoutRelatable
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
     * Polymorphic relation: layout_relations for this model (application + collection scoped).
     *
     * @return MorphMany<LayoutRelation>
     */
    public function layoutRelatable(): MorphMany
    {
        return $this->morphMany(LayoutRelation::class, 'relatable');
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
     *
     * @param Layout|int $layout
     * @param string $application
     * @param string|null $collection
     *
     * @return LayoutRelation
     */
    public function storeLayout(Layout|int $layout, string $application, ?string $collection = null): LayoutRelation
    {
        $layoutId = $layout instanceof Layout ? $layout->id : $layout;

        return $this->layoutRelatable()->create([
            'application' => $application,
            'layout_id'   => $layoutId,
            'collection'  => $collection,
        ]);
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
     * Resolve the effective Layout for this model for the given application and optional collection.
     * Uses layout_relations first; falls back to layout_pages (application + layoutPageType()) when no relation exists.
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

        return $layoutPage?->layout;
    }

    /**
     * Remove layout bindings for the given application and/or collection (null = all).
     *
     * @param string|null $application
     * @param string|null $collection
     *
     * @return static
     */
    public function forgetLayout(?string $application = null, ?string $collection = null): static
    {
        $this->layoutRelatable()
            ->when($application !== null, fn ($q) => $q->where('application', $application))
            ->when($collection !== null, fn ($q) => $q->where('collection', $collection))
            ->delete();

        return $this;
    }
}
