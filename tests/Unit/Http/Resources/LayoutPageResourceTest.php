<?php

namespace JobMetric\Layout\Tests\Unit\Http\Resources;

use Illuminate\Http\Request;
use JobMetric\Layout\Http\Resources\LayoutPageResource;
use JobMetric\Layout\Models\Layout;
use JobMetric\Layout\Models\LayoutPage;
use JobMetric\Layout\Tests\TestCase;

/**
 * Unit tests for LayoutPageResource (toArray: layout_id, application, page).
 */
class LayoutPageResourceTest extends TestCase
{
    /**
     * toArray returns layout_id, application, page.
     */
    public function test_toArray_contains_layout_id_application_page(): void
    {
        $layout = Layout::query()->create([
            'name'   => 'L',
            'status' => true,
        ]);
        $page = LayoutPage::query()->create([
            'layout_id'   => $layout->id,
            'application' => 'shop',
            'page'        => 'product',
        ]);

        $resource = new LayoutPageResource($page);
        $array = $resource->toArray(new Request);

        $this->assertSame($layout->id, $array['layout_id']);
        $this->assertSame('shop', $array['application']);
        $this->assertSame('product', $array['page']);
    }
}
