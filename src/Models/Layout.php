<?php

namespace JobMetric\Layout\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use JobMetric\PackageCore\Models\HasBooleanStatus;

/**
 * Class Layout
 *
 * Represents a layout definition that groups pages (application/page scope) and plugins
 * (with position and ordering). Can be bound to any model via the layout_relation pivot.
 * Use Laravel query builder: whereName(), whereStatus(), whereId(), etc.
 *
 * @package JobMetric\Layout
 *
 * @property int $id                         The primary identifier of the layout row.
 * @property string $name                    Display name of the layout.
 * @property bool $status                    Active flag (true=visible in listing, false=hidden).
 * @property Carbon|null $deleted_at         Soft delete timestamp.
 * @property Carbon $created_at              The timestamp when this layout was created.
 * @property Carbon $updated_at              The timestamp when this layout was last updated.
 *
 * @property-read int $layout_plugin_count
 * @property-read LayoutPage[] $layoutPages
 * @property-read LayoutPlugin[] $layoutPlugins
 * @property-read LayoutRelation[] $layoutRelations
 *
 * @method static Builder|Layout whereId(int $id)
 * @method static Builder|Layout whereName(string $name)
 * @method static Builder|Layout whereStatus(bool $status)
 */
class Layout extends Model
{
    use HasFactory, SoftDeletes, HasBooleanStatus;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'name'   => 'string',
        'status' => 'boolean',
    ];

    /**
     * Override the table name using config.
     *
     * @return string
     */
    public function getTable(): string
    {
        return config('layout.tables.layout', parent::getTable());
    }

    /**
     * Get pages (application/page pairs) assigned to this layout.
     *
     * @return HasMany
     */
    public function layoutPages(): HasMany
    {
        return $this->hasMany(LayoutPage::class);
    }

    /**
     * Get plugins (with position and ordering) assigned to this layout.
     *
     * @return HasMany
     */
    public function layoutPlugins(): HasMany
    {
        return $this->hasMany(LayoutPlugin::class);
    }

    /**
     * Get polymorphic bindings of this layout to relatable models.
     *
     * @return HasMany
     */
    public function layoutRelations(): HasMany
    {
        return $this->hasMany(LayoutRelation::class);
    }

    /**
     * Accessor: count of plugins attached to this layout.
     *
     * @return int
     */
    public function getLayoutPluginCountAttribute(): int
    {
        return $this->layoutPlugins()->count();
    }
}
