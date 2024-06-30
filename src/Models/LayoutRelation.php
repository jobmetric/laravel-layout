<?php

namespace JobMetric\Layout\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * JobMetric\Layout\Models\LayoutRelation
 *
 * @property string application
 * @property string relatable_type
 * @property int relatable_id
 * @property int layout_id
 * @property string collection
 *
 * @property mixed $relatable
 * @property Layout|null $layout
 *
 * @method static Builder|LayoutRelation byApplication(string $application)
 * @method static Builder|LayoutRelation byCollection(string $collection)
 * @method static Builder|LayoutRelation applicationCollection(string $application, string $collection)
 */
class LayoutRelation extends Pivot
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'application',
        'relatable_type',
        'relatable_id',
        'layout_id',
        'collection'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'application' => 'string',
        'relatable_type' => 'string',
        'relatable_id' => 'integer',
        'layout_id' => 'integer',
        'collection' => 'string'
    ];

    public function getTable()
    {
        return config('layout.tables.layout_relation', parent::getTable());
    }

    /**
     * Get the relatable model that owns the layout.
     *
     * @return MorphTo
     */
    public function relatable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the layout that owns the relation.
     *
     * @return BelongsTo
     */
    public function layout(): BelongsTo
    {
        return $this->belongsTo(Layout::class);
    }

    /**
     * Scope a query to only include layouts of a given application.
     *
     * @param Builder $query
     * @param string $application
     *
     * @return Builder
     */
    public function scopeByApplication(Builder $query, string $application): Builder
    {
        return $query->where('application', $application);
    }

    /**
     * Scope a query to only include layouts of a given collection.
     *
     * @param Builder $query
     * @param string $collection
     *
     * @return Builder
     */
    public function scopeByCollection(Builder $query, string $collection): Builder
    {
        return $query->where('collection', $collection);
    }

    /**
     * Scope a query to only include layouts of a given application and collection.
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
            'collection' => $collection
        ]);
    }
}
