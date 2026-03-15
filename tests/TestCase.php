<?php

namespace JobMetric\Layout\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use JobMetric\Layout\LayoutServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

/**
 * Base test case for Layout package.
 * Uses RefreshDatabase like Translation and Metadata packages; test tables via loadMigrationPath.
 */
abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * @param Application $app
     *
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            LayoutServiceProvider::class,
        ];
    }

    /**
     * @param Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('layout.tables', [
            'layout'          => 'layouts',
            'layout_page'     => 'layout_pages',
            'layout_plugin'   => 'layout_plugins',
            'layout_relation' => 'layout_relations',
        ]);
        $app['config']->set('layout.cache_time', 0);
        $app['config']->set('extension.tables.plugin', 'plugins');
    }

    protected function setUp(): void
    {
        parent::setUp();

        loadMigrationPath(__DIR__ . '/database/migrations');
    }
}
