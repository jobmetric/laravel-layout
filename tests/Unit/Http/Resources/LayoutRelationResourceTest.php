<?php

namespace JobMetric\Layout\Tests\Unit\Http\Resources;

use Illuminate\Http\Request;
use JobMetric\Layout\Http\Resources\LayoutRelationResource;
use JobMetric\Layout\Models\Layout;
use JobMetric\Layout\Models\LayoutRelation;
use JobMetric\Layout\Tests\TestCase;

/**
 * Unit tests for LayoutRelationResource (toArray: application, collection, layout_id, relatable).
 */
class LayoutRelationResourceTest extends TestCase
{
    /**
     * toArray returns application, collection, layout_id, relatable_type, relatable_id.
     */
    public function test_toArray_contains_application_collection_layout_id_relatable(): void
    {
        $layout = Layout::query()->create([
            'name'   => 'L',
            'status' => true,
        ]);
        $relation = LayoutRelation::query()->create([
            'application'    => 'shop',
            'relatable_type' => 'App\Models\Product',
            'relatable_id'   => 1,
            'layout_id'      => $layout->id,
            'collection'     => 'cat1',
        ]);

        $resource = new LayoutRelationResource($relation);
        $array = $resource->toArray(new Request);

        $this->assertSame('shop', $array['application']);
        $this->assertSame('cat1', $array['collection']);
        $this->assertSame($layout->id, $array['layout_id']);
        $this->assertArrayHasKey('relatable_type', $array);
        $this->assertArrayHasKey('relatable_id', $array);
    }
}
