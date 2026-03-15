<?php

namespace JobMetric\Layout\Tests\Feature\Services;

use Illuminate\Support\Facades\DB;
use JobMetric\Layout\Facades\ApplicationRegistry;
use JobMetric\Layout\Facades\Layout as LayoutFacade;
use JobMetric\Layout\Models\Layout;
use JobMetric\Layout\Tests\TestCase;
use JobMetric\PackageCore\Output\Response;

/**
 * Feature tests for Layout service (CRUD, applications, getPosition).
 */
class LayoutServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        ApplicationRegistry::register('shop', [
            'name'  => 'Shop',
            'order' => 1,
        ]);
    }

    protected function tearDown(): void
    {
        ApplicationRegistry::clear();
        parent::tearDown();
    }

    /**
     * store creates layout and persists layout_pages and layout_plugins; response ok with data.
     */
    public function test_store_creates_layout_with_pages_and_plugins(): void
    {
        $pluginId = (int) DB::connection('testing')->table('plugins')->insertGetId([]);

        $data = [
            'name'    => 'Main',
            'status'  => true,
            'pages'   => [
                [
                    'application' => 'shop',
                    'page'        => 'product',
                ],
            ],
            'plugins' => [
                [
                    'plugin_id' => $pluginId,
                    'position'  => 'header',
                    'ordering'  => 0,
                ],
            ],
        ];

        $response = LayoutFacade::store($data);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertTrue($response->ok);
        $this->assertNotNull($response->data);

        $layout = Layout::query()->where('name', 'Main')->first();
        $this->assertNotNull($layout);
        $this->assertSame(1, $layout->layoutPages()->count());
        $this->assertSame(1, $layout->layoutPlugins()->count());
    }

    /**
     * paginate returns success response with paginated layout data.
     */
    public function test_paginate_returns_paginated_layouts(): void
    {
        Layout::query()->create([
            'name'   => 'L1',
            'status' => true,
        ]);
        Layout::query()->create([
            'name'   => 'L2',
            'status' => true,
        ]);

        $response = LayoutFacade::paginate(10);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertTrue($response->ok);
        $this->assertNotNull($response->data);
    }

    /**
     * show returns success response with layout data for given id.
     */
    public function test_show_returns_layout_by_id(): void
    {
        $layout = Layout::query()->create([
            'name'   => 'ShowMe',
            'status' => true,
        ]);

        $response = LayoutFacade::show($layout->id);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertTrue($response->ok);
        $this->assertNotNull($response->data);
    }

    /**
     * update changes layout name and status; response ok and model refreshed.
     */
    public function test_update_modifies_layout(): void
    {
        $layout = Layout::query()->create([
            'name'   => 'Old',
            'status' => true,
        ]);

        $response = LayoutFacade::update($layout->id, [
            'name'   => 'Updated',
            'status' => false,
        ]);

        $this->assertTrue($response->ok);
        $layout->refresh();
        $this->assertSame('Updated', $layout->name);
        $this->assertFalse($layout->status);
    }

    /**
     * destroy soft-deletes layout; find returns null, withTrashed finds it.
     */
    public function test_destroy_soft_deletes_layout(): void
    {
        $layout = Layout::query()->create([
            'name'   => 'ToDelete',
            'status' => true,
        ]);

        $response = LayoutFacade::destroy($layout->id);

        $this->assertTrue($response->ok);
        $this->assertNull(Layout::query()->find($layout->id));
        $this->assertNotNull(Layout::query()->withTrashed()->find($layout->id));
    }

    /**
     * getApplications returns application keys from ApplicationRegistry.
     */
    public function test_getApplications_returns_registry_values(): void
    {
        $apps = LayoutFacade::getApplications();

        $this->assertIsArray($apps);
        $this->assertContains('shop', $apps);
    }

    /**
     * getApplicationsWithOptions returns all applications with options from registry.
     */
    public function test_getApplicationsWithOptions_returns_registry_all(): void
    {
        $apps = LayoutFacade::getApplicationsWithOptions();

        $this->assertIsArray($apps);
        $this->assertArrayHasKey('shop', $apps);
    }

    /**
     * getPosition returns distinct position values from layout plugins.
     */
    public function test_getPosition_returns_distinct_positions_for_layout(): void
    {
        $layout = Layout::query()->create(['name' => 'L', 'status' => true]);
        $pluginId = (int) DB::connection('testing')->table('plugins')->insertGetId([]);
        $layout->layoutPlugins()->create([
            'plugin_id' => $pluginId,
            'position'  => 'header',
            'ordering'  => 0,
        ]);
        $layout->layoutPlugins()->create([
            'plugin_id' => $pluginId,
            'position'  => 'footer',
            'ordering'  => 0,
        ]);

        $positions = LayoutFacade::getPosition($layout);

        $this->assertContains('header', $positions);
        $this->assertContains('footer', $positions);
    }

    /**
     * getPosition returns empty array when layout has no plugins.
     */
    public function test_getPosition_returns_empty_for_layout_without_plugins(): void
    {
        $layout = Layout::query()->create([
            'name'   => 'Empty',
            'status' => true,
        ]);

        $positions = LayoutFacade::getPosition($layout->id);

        $this->assertSame([], $positions);
    }
}
