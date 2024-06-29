<?php

namespace JobMetric\Layout\Tests;

use JobMetric\Extension\Facades\Extension;
use JobMetric\Extension\Facades\Plugin;
use JobMetric\Extension\Http\Resources\ExtensionResource;
use JobMetric\Layout\Facades\Layout;
use JobMetric\Layout\Http\Resources\LayoutResource;
use Tests\BaseDatabaseTestCase as BaseTestCase;

class LayoutTest extends BaseTestCase
{
    private function addPlugin(): array
    {
        Extension::install('Module', 'Banner');

        return Plugin::add('Module', 'Banner', [
            'title' => 'sample title',
            'status' => true,
            'fields' => [
                'width' => '100',
                'height' => '100',
            ]
        ]);
    }

    /**
     * @throw Throwable
     */
    public function testStore(): void
    {
        $plugin = $this->addPlugin();

        $layout = Layout::store([
            'name' => 'Test Layout',
            'status' => true,
            'pages' => [
                [
                    'application' => 'Test Application',
                    'page' => 'Test Page',
                ],
            ],
            'plugins' => [
                [
                    'plugin_id' => $plugin['data']->id,
                    'position' => 'top',
                    'ordering' => 1,
                ],
            ],
        ]);

        $this->assertIsArray($layout);
        $this->assertTrue($layout['ok']);
        $this->assertEquals($layout['message'], trans('layout::base.messages.created'));
        $this->assertInstanceOf(LayoutResource::class, $layout['data']);
        $this->assertEquals(201, $layout['status']);

        $this->assertDatabaseHas('layouts', [
            'name' => 'Test Layout',
            'status' => true,
        ]);

        $this->assertDatabaseHas('layout_pages', [
            'application' => 'Test Application',
            'page' => 'Test Page',
        ]);

        $this->assertDatabaseHas('layout_plugins', [
            'plugin_id' => $layout['data']->id,
            'position' => 'top',
            'ordering' => 1,
        ]);
    }

    /**
     * @throw Throwable
     */
    public function testUpdate(): void
    {
        $plugin = $this->addPlugin();

        $layout = Layout::store([
            'name' => 'Test Layout',
            'status' => true,
            'pages' => [
                [
                    'application' => 'Test Application',
                    'page' => 'Test Page',
                ],
            ],
            'plugins' => [
                [
                    'plugin_id' => $plugin['data']->id,
                    'position' => 'top',
                    'ordering' => 1,
                ],
            ],
        ]);

        $layout = Layout::update($layout['data']->id, [
            'name' => 'Test Layout Updated',
            'status' => false,
            'pages' => [
                [
                    'application' => 'Test Application Updated',
                    'page' => 'Test Page Updated',
                ],
            ],
            'plugins' => [
                [
                    'plugin_id' => $plugin['data']->id,
                    'position' => 'bottom',
                    'ordering' => 2,
                ],
            ],
        ]);

        $this->assertIsArray($layout);
        $this->assertTrue($layout['ok']);
        $this->assertEquals($layout['message'], trans('layout::base.messages.updated'));
        $this->assertInstanceOf(LayoutResource::class, $layout['data']);
        $this->assertEquals(200, $layout['status']);

        $this->assertDatabaseHas('layouts', [
            'name' => 'Test Layout Updated',
            'status' => false,
        ]);

        $this->assertDatabaseHas('layout_pages', [
            'application' => 'Test Application Updated',
            'page' => 'Test Page Updated',
        ]);

        $this->assertDatabaseHas('layout_plugins', [
            'plugin_id' => $layout['data']->id,
            'position' => 'bottom',
            'ordering' => 2,
        ]);

        $this->assertDatabaseMissing('layout_plugins', [
            'plugin_id' => $layout['data']->id,
            'position' => 'top',
            'ordering' => 1,
        ]);

        $this->assertDatabaseMissing('layout_pages', [
            'application' => 'Test Application',
            'page' => 'Test Page',
        ]);

        $this->assertDatabaseMissing('layout_plugins', [
            'plugin_id' => $layout['data']->id,
            'position' => 'top',
            'ordering' => 1,
        ]);
    }

    /**
     * @throw Throwable
     */
    public function testDelete(): void
    {
        $plugin = $this->addPlugin();

        $layout = Layout::store([
            'name' => 'Test Layout',
            'status' => true,
            'pages' => [
                [
                    'application' => 'Test Application',
                    'page' => 'Test Page',
                ],
            ],
            'plugins' => [
                [
                    'plugin_id' => $plugin['data']->id,
                    'position' => 'top',
                    'ordering' => 1,
                ],
            ],
        ]);

        $layout = Layout::delete($layout['data']->id);

        $this->assertIsArray($layout);
        $this->assertTrue($layout['ok']);
        $this->assertEquals($layout['message'], trans('layout::base.messages.deleted'));
        $this->assertEquals(200, $layout['status']);

        $this->assertSoftDeleted('layouts', [
            'name' => 'Test Layout',
            'status' => true,
        ]);

        $layout = Layout::delete($layout['data']->id);

        $this->assertIsArray($layout);
        $this->assertFalse($layout['ok']);
        $this->assertEquals($layout['message'], trans('layout::base.validation.errors'));
        $this->assertEquals(404, $layout['status']);
    }

    /**
     * @throw Throwable
     */
    public function testRestore(): void
    {
        $plugin = $this->addPlugin();

        $layout = Layout::store([
            'name' => 'Test Layout',
            'status' => true,
            'pages' => [
                [
                    'application' => 'Test Application',
                    'page' => 'Test Page',
                ],
            ],
            'plugins' => [
                [
                    'plugin_id' => $plugin['data']->id,
                    'position' => 'top',
                    'ordering' => 1,
                ],
            ],
        ]);

        $layout = Layout::delete($layout['data']->id);

        $layout = Layout::restore($layout['data']->id);

        $this->assertIsArray($layout);
        $this->assertTrue($layout['ok']);
        $this->assertEquals($layout['message'], trans('layout::base.messages.restored'));
        $this->assertEquals(200, $layout['status']);

        $this->assertDatabaseHas('layouts', [
            'name' => 'Test Layout',
            'status' => true,
        ]);

        $layout = Layout::restore($layout['data']->id);

        $this->assertIsArray($layout);
        $this->assertFalse($layout['ok']);
        $this->assertEquals($layout['message'], trans('layout::base.validation.errors'));
        $this->assertEquals(404, $layout['status']);
    }

    /**
     * @throw Throwable
     */
    public function testForceDelete(): void
    {
        $plugin = $this->addPlugin();

        $layout = Layout::store([
            'name' => 'Test Layout',
            'status' => true,
            'pages' => [
                [
                    'application' => 'Test Application',
                    'page' => 'Test Page',
                ],
            ],
            'plugins' => [
                [
                    'plugin_id' => $plugin['data']->id,
                    'position' => 'top',
                    'ordering' => 1,
                ],
            ],
        ]);

        $layout = Layout::delete($layout['data']->id);

        $layout = Layout::forceDelete($layout['data']->id);

        $this->assertIsArray($layout);
        $this->assertTrue($layout['ok']);
        $this->assertEquals($layout['message'], trans('layout::base.messages.permanently_deleted'));
        $this->assertEquals(200, $layout['status']);

        $this->assertDatabaseMissing('layouts', [
            'name' => 'Test Layout',
            'status' => true,
        ]);

        $this->assertDatabaseMissing('layout_pages', [
            'application' => 'Test Application',
            'page' => 'Test Page',
        ]);

        $this->assertDatabaseMissing('layout_plugins', [
            'plugin_id' => $layout['data']->id,
            'position' => 'top',
            'ordering' => 1,
        ]);

        $layout = Layout::forceDelete($layout['data']->id);

        $this->assertIsArray($layout);
        $this->assertFalse($layout['ok']);
        $this->assertEquals($layout['message'], trans('layout::base.validation.errors'));
        $this->assertEquals(404, $layout['status']);
    }

    /**
     * @throw Throwable
     */
    public function testGet(): void
    {
        $plugin = $this->addPlugin();

        $layout = Layout::store([
            'name' => 'Test Layout',
            'status' => true,
            'pages' => [
                [
                    'application' => 'Test Application',
                    'page' => 'Test Page',
                ],
            ],
            'plugins' => [
                [
                    'plugin_id' => $plugin['data']->id,
                    'position' => 'top',
                    'ordering' => 1,
                ],
            ],
        ]);

        $layout = Layout::get($layout['data']->id);

        $this->assertIsArray($layout);
        $this->assertTrue($layout['ok']);
        $this->assertEquals($layout['message'], trans('layout::base.messages.found'));
        $this->assertInstanceOf(LayoutResource::class, $layout['data']);
        $this->assertEquals(200, $layout['status']);
    }

    /**
     * @throw Throwable
     */
    public function testAll(): void
    {
        $plugin = $this->addPlugin();

        $layout = Layout::store([
            'name' => 'Test Layout',
            'status' => true,
            'pages' => [
                [
                    'application' => 'Test Application',
                    'page' => 'Test Page',
                ],
            ],
            'plugins' => [
                [
                    'plugin_id' => $plugin['data']->id,
                    'position' => 'top',
                    'ordering' => 1,
                ],
            ],
        ]);

        $layouts = Layout::all();

        $this->assertCount(1, $layouts);

        $layouts->each(function ($layout) {
            $this->assertInstanceOf(LayoutResource::class, $layout);
        });
    }

    /**
     * @throw Throwable
     */
    public function testPaginate(): void
    {
        $plugin = $this->addPlugin();

        $layout = Layout::store([
            'name' => 'Test Layout',
            'status' => true,
            'pages' => [
                [
                    'application' => 'Test Application',
                    'page' => 'Test Page',
                ],
            ],
            'plugins' => [
                [
                    'plugin_id' => $plugin['data']->id,
                    'position' => 'top',
                    'ordering' => 1,
                ],
            ],
        ]);

        $layouts = Layout::paginate();

        $this->assertCount(1, $layouts);

        $layouts->each(function ($layout) {
            $this->assertInstanceOf(LayoutResource::class, $layout);
        });

        $this->assertIsInt($layouts->total());
        $this->assertIsInt($layouts->perPage());
        $this->assertIsInt($layouts->currentPage());
        $this->assertIsInt($layouts->lastPage());
        $this->assertIsArray($layouts->items());
    }
}
