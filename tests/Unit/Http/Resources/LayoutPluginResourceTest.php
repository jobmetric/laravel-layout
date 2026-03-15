<?php

namespace JobMetric\Layout\Tests\Unit\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JobMetric\Layout\Http\Resources\LayoutPluginResource;
use JobMetric\Layout\Models\Layout;
use JobMetric\Layout\Models\LayoutPlugin;
use JobMetric\Layout\Tests\TestCase;

/**
 * Unit tests for LayoutPluginResource (toArray: layout_id, plugin_id, position, ordering).
 */
class LayoutPluginResourceTest extends TestCase
{
    /**
     * toArray returns layout_id, plugin_id, position, ordering.
     */
    public function test_toArray_contains_layout_id_plugin_id_position_ordering(): void
    {
        $layout = Layout::query()->create([
            'name'   => 'L',
            'status' => true,
        ]);
        $pluginId = (int) DB::connection('testing')->table('plugins')->insertGetId([]);

        $layoutPlugin = LayoutPlugin::query()->create([
            'layout_id' => $layout->id,
            'plugin_id' => $pluginId,
            'position'  => 'footer',
            'ordering'  => 1,
        ]);

        $resource = new LayoutPluginResource($layoutPlugin);
        $array = $resource->toArray(new Request);

        $this->assertSame($layout->id, $array['layout_id']);
        $this->assertSame($pluginId, $array['plugin_id']);
        $this->assertSame('footer', $array['position']);
        $this->assertSame(1, $array['ordering']);
    }
}
