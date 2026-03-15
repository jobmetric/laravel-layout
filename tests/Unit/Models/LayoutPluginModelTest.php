<?php

namespace JobMetric\Layout\Tests\Unit\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use JobMetric\Layout\Models\Layout;
use JobMetric\Layout\Models\LayoutPlugin;
use JobMetric\Layout\Tests\TestCase;

/**
 * Unit tests for LayoutPlugin model (table, fillable, layout relation, create, no timestamps).
 */
class LayoutPluginModelTest extends TestCase
{
    /**
     * getTable returns config layout_plugins table name.
     */
    public function test_getTable_returns_config_value(): void
    {
        $this->assertSame('layout_plugins', (new LayoutPlugin)->getTable());
    }

    /**
     * fillable contains layout_id, plugin_id, position, ordering.
     */
    public function test_fillable_contains_expected_attributes(): void
    {
        $fillable = (new LayoutPlugin)->getFillable();

        $this->assertContains('layout_id', $fillable);
        $this->assertContains('plugin_id', $fillable);
        $this->assertContains('position', $fillable);
        $this->assertContains('ordering', $fillable);
    }

    /**
     * layout relation returns BelongsTo Layout.
     */
    public function test_layout_relation_returns_belongs_to(): void
    {
        $plugin = new LayoutPlugin;
        $relation = $plugin->layout();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertSame(Layout::class, $relation->getRelated()::class);
    }

    /**
     * create persists layout_plugin with layout_id, plugin_id, position, ordering.
     */
    public function test_create_layout_plugin(): void
    {
        $layout = Layout::query()->create(['name' => 'L', 'status' => true]);
        $pluginId = (int) DB::connection('testing')->table('plugins')->insertGetId([]);

        $layoutPlugin = LayoutPlugin::query()->create([
            'layout_id' => $layout->id,
            'plugin_id' => $pluginId,
            'position'  => 'header',
            'ordering'  => 0,
        ]);

        $this->assertSame($layout->id, $layoutPlugin->layout_id);
        $this->assertSame('header', $layoutPlugin->position);
        $this->assertSame(0, $layoutPlugin->ordering);
    }

    /**
     * LayoutPlugin pivot has no timestamps (CREATED_AT, UPDATED_AT null).
     */
    public function test_no_timestamps(): void
    {
        $this->assertNull(LayoutPlugin::CREATED_AT);
        $this->assertNull(LayoutPlugin::UPDATED_AT);
    }
}
