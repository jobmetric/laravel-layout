<?php

namespace JobMetric\Layout;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use JobMetric\Layout\Events\LayoutRelationStoreEvent;
use JobMetric\Layout\Exceptions\CollectionPropertyNotExistException;
use JobMetric\Layout\Exceptions\ModelLayoutContractNotFoundException;
use JobMetric\Layout\Models\Layout;
use JobMetric\Layout\Models\LayoutPage;
use JobMetric\Layout\Models\LayoutRelation;
use Throwable;

/**
 * Trait HasLayout
 *
 * @package JobMetric\Layout
 *
 * @property Layout layout
 *
 * @method belongsTo(string $class)
 */
trait HasLayout
{
    /**
     * boot has layout trait
     *
     * @return void
     * @throws Throwable
     */
    public static function bootHasLayout(): void
    {
        if (!in_array('JobMetric\Layout\Contracts\LayoutContract', class_implements(self::class))) {
            throw new ModelLayoutContractNotFoundException(self::class);
        }
    }

    /**
     * Layout has one relationship
     *
     * @return BelongsTo
     * @throws Throwable
     */
    public function layout(): BelongsTo
    {
        return $this->belongsTo(Layout::class);
    }

    /**
     * layout has many relationships
     *
     * @return MorphMany
     */
    public function layoutRelatable(): MorphMany
    {
        return $this->morphMany(LayoutRelation::class, 'relatable');
    }

    /**
     * Get collection value
     *
     * @return string|null
     * @throws Throwable
     */
    public function layoutGetCollectionValue(): ?string
    {
        $field = $this->layoutCollectionField();

        if ($field === null) {
            return null;
        }

        if (!array_key_exists($field, $this->getAttributes())) {
            throw new CollectionPropertyNotExistException(self::class, $field);
        }

        return $this->{$field};
    }

    /**
     * scope layout for select application relationship
     *
     * @param string $application
     *
     * @return MorphMany
     */
    public function layoutApplicationTo(string $application): MorphMany
    {
        return $this->layoutRelatable()->where('application', $application);
    }

    /**
     * scope layout for select collection relationship
     *
     * @param string $collection
     *
     * @return MorphMany
     */
    public function layoutCollectionTo(string $collection): MorphMany
    {
        return $this->layoutRelatable()->where('collection', $collection);
    }

    /**
     * scope layout for select application and collection relationship
     *
     * @param string $application
     * @param string|null $collection
     *
     * @return MorphMany
     */
    public function layoutApplicationCollectionTo(string $application, string $collection = null): MorphMany
    {
        return $this->layoutRelatable()->where([
            'application' => $application,
            'collection' => $collection,
        ]);
    }

    /**
     * Store layout
     *
     * @param Layout|int $layout
     * @param string $application
     * @param string|null $collection
     *
     * @return LayoutRelation
     * @throws Throwable
     */
    public function storeLayout(Layout|int $layout, string $application, string $collection = null): LayoutRelation
    {
        $layout_relation = $this->layoutRelatable()->create([
            'application' => $application,
            'layout_id' => $layout instanceof Layout ? $layout->id : $layout,
            'collection' => $collection,
        ]);

        event(new LayoutRelationStoreEvent($this, $layout_relation));

        return $layout_relation;
    }

    /**
     * load layout relatable after model loaded
     *
     * @param string|null $application
     * @param string|null $collection
     *
     * @return static
     */
    public function withLayoutRelatable(string $application = null, string $collection = null): static
    {
        if (is_null($application)) {
            $this->load('layoutRelatable');
        } else if (is_null($collection)) {
            $this->load(['layoutRelatable' => function ($query) use ($application) {
                $query->where('application', $application);
            }]);
        } else {
            $this->load(['layoutRelatable' => function ($query) use ($application, $collection) {
                $query->where([
                    'application' => $application,
                    'collection' => $collection
                ]);
            }]);
        }

        return $this;
    }

    /**
     * get layout
     *
     * @param string|null $application
     * @param string|null $collection
     *
     * @return Layout|null
     */
    public function getLayout(string $application = null, string $collection = null): ?Layout
    {
        /**
         * @var LayoutRelation $layout_relation
         */
        $layout_relation = $this->layoutRelatable()
            ->when($application, fn($query) => $query->where('application', $application))
            ->when($collection, fn($query) => $query->where('collection', $collection))
            ->first();

        if ($layout_relation) {
            return $layout_relation->layout;
        }

        /**
         * @var LayoutPage $layout_page
         */
        $layout_page = LayoutPage::applicationPage($application, $this->layoutPageType())->first();

        return $layout_page?->layout;
    }

    /**
     * forget layout
     *
     * @param string|null $application
     * @param string|null $collection
     *
     * @return static
     */
    public function forgetLayout(string $application = null, string $collection = null): static
    {
        $this->layoutRelatable()
            ->when($application, fn($query) => $query->where('application', $application))
            ->when($collection, fn($query) => $query->where('collection', $collection))
            ->delete();

        return $this;
    }
}
