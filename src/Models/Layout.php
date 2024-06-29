<?php

namespace JobMetric\Layout\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JobMetric\PackageCore\Models\HasBooleanStatus;

/**
 * JobMetric\Layout\Models\Layout
 *
 * @property int id
 * @property string name
 * @property bool status
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 */
class Layout extends Model
{
    use HasFactory, HasBooleanStatus;

    protected $fillable = [
        'name',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'name' => 'string',
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function getTable()
    {
        return config('layout.tables.layout', parent::getTable());
    }

    /**
     * Get the layout pages.
     *
     * @return HasMany
     */
    public function layoutPages(): HasMany
    {
        return $this->hasMany(LayoutPage::class);
    }

    /**
     * Get the layout extension.
     *
     * @return HasMany
     */
    public function layoutExtension(): HasMany
    {
        return $this->hasMany(LayoutExtension::class);
    }
}
