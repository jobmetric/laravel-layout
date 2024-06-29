<?php

namespace JobMetric\Layout\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use JobMetric\Extension\Models\Plugin;

/**
 * JobMetric\Layout\Models\LayoutPlugin
 *
 * @property int layout_id
 * @property int plugin_id
 * @property string position
 * @property int ordering
 * @property Layout layout
 * @property Plugin plugin
 * @method static Builder byPlugin(int $plugin_id)
 * @method static Builder byPosition(string $position)
 */
class LayoutPlugin extends Pivot
{
    use HasFactory;

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'layout_id',
        'plugin_id',
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
        'plugin_id' => 'integer',
        'position' => 'string',
        'ordering' => 'integer',
    ];

    public function getTable()
    {
        return config('layout.tables.layout_plugin', parent::getTable());
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
     * Get the plugin that owns the relation.
     *
     * @return BelongsTo
     */
    public function plugin(): BelongsTo
    {
        return $this->belongsTo(Plugin::class);
    }

    /**
     * Get the plugin model.
     *
     * @param Builder $query
     * @param int $plugin_id
     *
     * @return Builder
     */
    public function scopeByPlugin(Builder $query, int $plugin_id): Builder
    {
        return $query->where('plugin_id', $plugin_id);
    }

    /**
     * Get the position model.
     *
     * @param Builder $query
     * @param string $position
     *
     * @return Builder
     */
    public function scopeByPosition(Builder $query, string $position): Builder
    {
        return $query->where('position', $position);
    }
}
