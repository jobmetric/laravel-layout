<?php

namespace JobMetric\Layout\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * JobMetric\Layout\Models\LayoutPage
 *
 * @property int layout_id
 * @property string application
 * @property string page
 * @property Layout layout
 * @method static Builder applicationPage(string $application, string $page)
 */
class LayoutPage extends Pivot
{
    use HasFactory;

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'layout_id',
        'application',
        'page'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'layout_id' => 'integer',
        'application' => 'string',
        'page' => 'string'
    ];

    public function getTable()
    {
        return config('layout.tables.layout_page', parent::getTable());
    }

    /**
     * Get the layout that owns the path.
     *
     * @return BelongsTo
     */
    public function layout(): BelongsTo
    {
        return $this->belongsTo(Layout::class);
    }

    /**
     * Scope a query to only include layout pages by application and page field.
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
            'page' => $page
        ]);
    }
}
