<?php

namespace JobMetric\Layout\Tests\Feature;

use JobMetric\Layout\Facades\ApplicationRegistry;
use JobMetric\Layout\Facades\Layout;
use JobMetric\Layout\LayoutServiceProvider;
use JobMetric\Layout\Tests\TestCase;

/**
 * Feature tests for LayoutServiceProvider (facades, config, provider registration).
 */
class LayoutServiceProviderTest extends TestCase
{
    /**
     * Layout facade resolves to Layout service instance.
     */
    public function test_layout_facade_resolves_service(): void
    {
        $service = Layout::getFacadeRoot();

        $this->assertNotNull($service);
    }

    /**
     * ApplicationRegistry facade resolves to singleton; same instance on multiple calls.
     */
    public function test_application_registry_facade_resolves_singleton(): void
    {
        $registry = ApplicationRegistry::getFacadeRoot();

        $this->assertNotNull($registry);
        $this->assertSame(ApplicationRegistry::getFacadeRoot(), $registry);
    }

    /**
     * layout config tables (layout, layout_page, layout_plugin, layout_relation) are published/loaded.
     */
    public function test_config_layout_tables_are_loaded(): void
    {
        $this->assertSame('layouts', config('layout.tables.layout'));
        $this->assertSame('layout_pages', config('layout.tables.layout_page'));
        $this->assertSame('layout_plugins', config('layout.tables.layout_plugin'));
        $this->assertSame('layout_relations', config('layout.tables.layout_relation'));
    }

    /**
     * LayoutServiceProvider is registered in application loaded providers.
     */
    public function test_provider_is_registered(): void
    {
        $providers = $this->app->getLoadedProviders();

        $this->assertArrayHasKey(LayoutServiceProvider::class, $providers);
        $this->assertTrue($providers[LayoutServiceProvider::class]);
    }
}
