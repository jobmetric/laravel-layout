<?php

namespace JobMetric\Layout\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use JobMetric\Extension\Models\Plugin;

/**
 * Class LayoutPlugin
 *
 * Pivot: binds an extension plugin to a layout at a given position with an ordering.
 * Same plugin can appear in multiple positions; ordering controls display/execution order.
 * Use Laravel query builder: whereLayoutId(), wherePluginId(), wherePosition(), whereOrdering(), etc.
 *
 * @package JobMetric\Layout
 *
 * @property int $layout_id                   The owning layout identifier.
 * @property int $plugin_id                   The extension plugin identifier.
 * @property string $position                 Placement key (e.g. header, footer).
 * @property int $ordering                    Display/execution order at this position; lower runs first.
 *
 * @property-read Layout $layout
 * @property-read Plugin $plugin
 *
 * @method static Builder|LayoutPlugin whereLayoutId(int $layout_id)
 * @method static Builder|LayoutPlugin wherePluginId(int $plugin_id)
 * @method static Builder|LayoutPlugin wherePosition(string $position)
 * @method static Builder|LayoutPlugin whereOrdering(int $ordering)
 */
class LayoutPlugin extends Pivot
{
    use HasFactory;

    public const CREATED_AT = null;
    public const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'layout_id',
        'plugin_id',
        'position',
        'ordering',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'layout_id' => 'integer',
        'plugin_id' => 'integer',
        'position'  => 'string',
        'ordering'  => 'integer',
    ];

    /**
     * Override the table name using config.
     *
     * @return string
     */
    public function getTable(): string
    {
        return config('layout.tables.layout_plugin', parent::getTable());
    }

    /**
     * Get the owning layout.
     *
     * @return BelongsTo
     */
    public function layout(): BelongsTo
    {
        return $this->belongsTo(Layout::class);
    }

    /**
     * Get the extension plugin instance.
     *
     * @return BelongsTo
     */
    public function plugin(): BelongsTo
    {
        return $this->belongsTo(Plugin::class);
    }
}
