<?php

namespace JobMetric\Layout\Tests\Unit\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use JobMetric\Layout\Models\Layout;
use JobMetric\Layout\Models\LayoutPage;
use JobMetric\Layout\Models\LayoutPlugin;
use JobMetric\Layout\Models\LayoutRelation;
use JobMetric\Layout\Tests\TestCase;

/**
 * Unit tests for Layout model (table, fillable, relations, create, accessor, soft deletes).
 */
class LayoutModelTest extends TestCase
{
    /**
     * getTable returns config layout table name.
     */
    public function test_getTable_returns_config_value(): void
    {
        $this->assertSame('layouts', (new Layout)->getTable());
    }

    /**
     * fillable contains name and status.
     */
    public function test_fillable_contains_name_and_status(): void
    {
        $fillable = (new Layout)->getFillable();

        $this->assertContains('name', $fillable);
        $this->assertContains('status', $fillable);
    }

    /**
     * layoutPages relation returns HasMany to LayoutPage.
     */
    public function test_layoutPages_relation_returns_has_many(): void
    {
        $layout = new Layout;
        $relation = $layout->layoutPages();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertSame(LayoutPage::class, $relation->getRelated()::class);
    }

    /**
     * layoutPlugins relation returns HasMany to LayoutPlugin.
     */
    public function test_layoutPlugins_relation_returns_has_many(): void
    {
        $layout = new Layout;
        $relation = $layout->layoutPlugins();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertSame(LayoutPlugin::class, $relation->getRelated()::class);
    }

    /**
     * layoutRelations relation returns HasMany to LayoutRelation.
     */
    public function test_layoutRelations_relation_returns_has_many(): void
    {
        $layout = new Layout;
        $relation = $layout->layoutRelations();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertSame(LayoutRelation::class, $relation->getRelated()::class);
    }

    /**
     * create persists layout and retrieve returns model with name and status.
     */
    public function test_create_and_retrieve_layout(): void
    {
        $layout = Layout::query()->create([
            'name'   => 'Main Layout',
            'status' => true,
        ]);

        $this->assertGreaterThan(0, $layout->id);
        $this->assertSame('Main Layout', $layout->name);
        $this->assertTrue($layout->status);
    }

    /**
     * layout_plugin_count accessor returns count of related layoutPlugins.
     */
    public function test_layout_plugin_count_accessor_returns_count(): void
    {
        $layout = Layout::query()->create([
            'name'   => 'Test',
            'status' => true,
        ]);

        $this->assertSame(0, $layout->layout_plugin_count);
    }

    /**
     * delete soft-deletes layout; find excludes it, withTrashed finds it.
     */
    public function test_soft_deletes_apply(): void
    {
        $layout = Layout::query()->create([
            'name'   => 'Soft',
            'status' => true,
        ]);
        $id = $layout->id;

        $layout->delete();

        $this->assertNull(Layout::query()->find($id));
        $this->assertNotNull(Layout::query()->withTrashed()->find($id));
    }
}
