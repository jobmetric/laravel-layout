<?php

namespace JobMetric\Layout\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * JobMetric\Layout\Models\LayoutExtension
 *
 * @property int layout_id
 * @property string extension
 * @property string position
 * @property int ordering
 */
class LayoutExtension extends Model
{
    use HasFactory;

    protected $fillable = [
        'layout_id',
        'extension',
        'position',
        'ordering'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'layout_id' => 'integer',
        'extension' => 'string',
        'position' => 'string',
        'ordering' => 'integer',
    ];

    public function getTable()
    {
        return config('layout.tables.layout_extension', parent::getTable());
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
     * Get the extension model.
     *
     * @param Builder $query
     * @param string $extension
     * @return Builder
     */
    public function scopeByExtension(Builder $query, string $extension): Builder
    {
        return $query->where('extension', $extension);
    }

    /**
     * Get the position model.
     *
     * @param Builder $query
     * @param string $position
     * @return Builder
     */
    public function scopeByPosition(Builder $query, string $position): Builder
    {
        return $query->where('position', $position);
    }
}
