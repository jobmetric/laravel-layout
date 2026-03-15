<?php

namespace JobMetric\Layout\Tests\Unit\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use JobMetric\Layout\Models\Layout;
use JobMetric\Layout\Models\LayoutPage;
use JobMetric\Layout\Tests\TestCase;

/**
 * Unit tests for LayoutPage model (table, fillable, layout relation, scopeApplicationPage, no timestamps).
 */
class LayoutPageModelTest extends TestCase
{
    /**
     * getTable returns config layout_pages table name.
     */
    public function test_getTable_returns_config_value(): void
    {
        $this->assertSame('layout_pages', (new LayoutPage)->getTable());
    }

    /**
     * fillable contains layout_id, application, page.
     */
    public function test_fillable_contains_layout_id_application_page(): void
    {
        $fillable = (new LayoutPage)->getFillable();

        $this->assertContains('layout_id', $fillable);
        $this->assertContains('application', $fillable);
        $this->assertContains('page', $fillable);
    }

    /**
     * layout relation returns BelongsTo Layout.
     */
    public function test_layout_relation_returns_belongs_to(): void
    {
        $page = new LayoutPage;
        $relation = $page->layout();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertSame(Layout::class, $relation->getRelated()::class);
    }

    /**
     * scopeApplicationPage filters by application and page.
     */
    public function test_scopeApplicationPage_filters_by_application_and_page(): void
    {
        $layout = Layout::query()->create([
            'name'   => 'L',
            'status' => true,
        ]);
        LayoutPage::query()->create([
            'layout_id'   => $layout->id,
            'application' => 'shop',
            'page'        => 'product',
        ]);
        LayoutPage::query()->create([
            'layout_id'   => $layout->id,
            'application' => 'shop',
            'page'        => 'category',
        ]);

        $found = LayoutPage::query()->applicationPage('shop', 'product')->first();

        $this->assertNotNull($found);
        $this->assertSame('product', $found->page);
    }

    /**
     * LayoutPage pivot has no timestamps (CREATED_AT, UPDATED_AT null).
     */
    public function test_no_timestamps(): void
    {
        $this->assertNull(LayoutPage::CREATED_AT);
        $this->assertNull(LayoutPage::UPDATED_AT);
    }
}
