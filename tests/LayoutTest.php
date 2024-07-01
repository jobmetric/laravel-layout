<?php

namespace JobMetric\Layout\Tests;

use JobMetric\Layout\Facades\Layout;
use JobMetric\Layout\Http\Resources\LayoutResource;

class LayoutTest extends BaseLayout
{
    /**
     * @throw Throwable
     */
    public function test_store(): void
    {
        $layout = $this->addLayout();

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
    public function test_update(): void
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
    public function test_delete(): void
    {
        $layout = $this->addLayout();

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
    public function test_restore(): void
    {
        $layout = $this->addLayout();

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
    public function test_force_delete(): void
    {
        $layout = $this->addLayout();

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
    public function test_get(): void
    {
        $layout = $this->addLayout();

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
    public function test_all(): void
    {
        $this->addLayout();

        $layouts = Layout::all();

        $this->assertCount(1, $layouts);

        $layouts->each(function ($layout) {
            $this->assertInstanceOf(LayoutResource::class, $layout);
        });
    }

    /**
     * @throw Throwable
     */
    public function test_paginate(): void
    {
        $this->addLayout();

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

    /**
     * @throw Throwable
     */
    public function test_get_position(): void
    {
        $layout = $this->addLayout();

        $layouts = Layout::getPosition($layout['data']->id);

        $this->assertCount(1, $layouts);
        $this->assertIsArray($layouts);
        $this->assertEquals('top', $layouts[0]);
    }

    /**
     * @throw Throwable
     */
    public function test_run_plugins(): void
    {
        $layout = $this->addLayout();

        $plugins = Layout::runPlugins($layout['data']->id);

        $this->assertIsArray($plugins);
        $this->assertArrayHasKey('top', $plugins);
        $this->assertEquals('Handle the extension', $plugins['top'][0]);
    }
}
