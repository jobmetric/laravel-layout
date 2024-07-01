<?php

namespace JobMetric\Layout\Tests;

use App\Models\Product;
use JobMetric\Extension\Facades\Extension;
use JobMetric\Extension\Facades\Plugin;
use JobMetric\Layout\Facades\Layout;
use Tests\BaseDatabaseTestCase as BaseTestCase;

class BaseLayout extends BaseTestCase
{
    public function addPlugin(): array
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

    public function addLayout(): array
    {
        $plugin = $this->addPlugin();

        return Layout::store([
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
    }

    public function addProduct(): Product
    {
        return Product::create([
            'status' => true,
        ]);
    }
}
