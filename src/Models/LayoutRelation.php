<?php

namespace JobMetric\Layout\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Class LayoutRelation
 *
 * Pivot: polymorphic binding of a layout to a relatable entity (e.g. Product, Post)
 * per application/collection. One entity can have one layout per (application, collection).
 * Use Laravel query builder: whereApplication(), whereCollection(), whereLayoutId(), etc.
 *
 * @package JobMetric\Layout
 *
 * @property string|null $application         Application/context key; null for base app.
 * @property string $relatable_type           The class name of the related model.
 * @property int $relatable_id                The ID of the related model instance.
 * @property int $layout_id                   The assigned layout identifier.
 * @property string|null $collection          Collection key; null for base collection.
 *
 * @property-read Model $relatable
 * @property-read Layout $layout
 *
 * @method static Builder|LayoutRelation whereApplication(?string $application)
 * @method static Builder|LayoutRelation whereCollection(?string $collection)
 * @method static Builder|LayoutRelation whereLayoutId(int $layout_id)
 * @method static Builder|LayoutRelation whereRelatableType(string $relatable_type)
 * @method static Builder|LayoutRelation whereRelatableId(int $relatable_id)
 * @method static Builder|LayoutRelation applicationCollection(string $application, string $collection)
 */
class LayoutRelation extends Pivot
{
    use HasFactory;

    /**
     * This table does not have Laravel's created_at/updated_at columns.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'application',
        'relatable_type',
        'relatable_id',
        'layout_id',
        'collection',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'application'    => 'string',
        'relatable_type' => 'string',
        'relatable_id'   => 'integer',
        'layout_id'      => 'integer',
        'collection'     => 'string',
    ];

    /**
     * Override the table name using config.
     *
     * @return string
     */
    public function getTable(): string
    {
        return config('layout.tables.layout_relation', parent::getTable());
    }

    /**
     * Get the relatable model (polymorphic).
     *
     * @return MorphTo
     */
    public function relatable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the assigned layout.
     *
     * @return BelongsTo
     */
    public function layout(): BelongsTo
    {
        return $this->belongsTo(Layout::class);
    }

    /**
     * Scope: filter by application and collection together.
     *
     * @param Builder $query
     * @param string $application
     * @param string $collection
     *
     * @return Builder
     */
    public function scopeApplicationCollection(Builder $query, string $application, string $collection): Builder
    {
        return $query->where([
            'application' => $application,
            'collection'  => $collection,
        ]);
    }
}
