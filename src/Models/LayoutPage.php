<?php

namespace JobMetric\Layout\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Class LayoutPage
 *
 * Pivot: binds a layout to an application/page pair (scope where the layout applies).
 * Null application or page means base app or base collection.
 * Use Laravel query builder: whereLayoutId(), whereApplication(), wherePage(), etc.
 *
 * @package JobMetric\Layout
 *
 * @property int $layout_id                   The owning layout identifier.
 * @property string|null $application         Application/context key; null for base app.
 * @property string|null $page                Page/collection key; null for base collection.
 *
 * @property-read Layout $layout
 *
 * @method static Builder|LayoutPage whereLayoutId(int $layout_id)
 * @method static Builder|LayoutPage whereApplication(?string $application)
 * @method static Builder|LayoutPage wherePage(?string $page)
 * @method static Builder|LayoutPage applicationPage(string $application, string $page)
 */
class LayoutPage extends Pivot
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
        'application',
        'page',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'layout_id'   => 'integer',
        'application' => 'string',
        'page'        => 'string',
    ];

    /**
     * Override the table name using config.
     *
     * @return string
     */
    public function getTable(): string
    {
        return config('layout.tables.layout_page', parent::getTable());
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
     * Scope: filter by application and page together.
     *
     * @param Builder $query
     * @param string $application
     * @param string $page
     *
     * @return Builder
     */
    public function scopeApplicationPage(Builder $query, string $application, string $page): Builder
    {
        return $query->where([
            'application' => $application,
            'page'        => $page,
        ]);
    }
}
