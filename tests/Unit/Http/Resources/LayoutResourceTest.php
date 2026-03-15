<?php

namespace JobMetric\Layout\Tests\Unit\Http\Resources;

use Illuminate\Http\Request;
use JobMetric\Layout\Http\Resources\LayoutResource;
use JobMetric\Layout\Models\Layout;
use JobMetric\Layout\Tests\TestCase;

/**
 * Unit tests for LayoutResource (toArray output: id, name, status, dates, layout_plugin_count).
 */
class LayoutResourceTest extends TestCase
{
    /**
     * toArray returns id, name, status, created_at, updated_at, deleted_at.
     */
    public function test_toArray_contains_id_name_status_dates(): void
    {
        $layout = Layout::query()->create([
            'name'   => 'Test Layout',
            'status' => true,
        ]);

        $resource = new LayoutResource($layout);
        $array = $resource->toArray(new Request);

        $this->assertSame($layout->id, $array['id']);
        $this->assertSame('Test Layout', $array['name']);
        $this->assertTrue($array['status']);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertArrayHasKey('updated_at', $array);
        $this->assertArrayHasKey('deleted_at', $array);
    }

    /**
     * toArray includes layout_plugin_count when layoutPlugins relation is loaded.
     */
    public function test_toArray_includes_layout_plugin_count_when_loaded(): void
    {
        $layout = Layout::query()->create([
            'name'   => 'L',
            'status' => true,
        ]);
        $layout->load('layoutPlugins');

        $resource = new LayoutResource($layout);
        $array = $resource->toArray(new Request);

        $this->assertArrayHasKey('layout_plugin_count', $array);
    }
}
