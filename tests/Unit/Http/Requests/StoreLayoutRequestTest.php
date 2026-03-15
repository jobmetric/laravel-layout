<?php

namespace JobMetric\Layout\Tests\Unit\Http\Requests;

use JobMetric\Layout\Http\Requests\StoreLayoutRequest;
use JobMetric\Layout\Tests\TestCase;

/**
 * Unit tests for StoreLayoutRequest (authorize, rules, attributes).
 */
class StoreLayoutRequestTest extends TestCase
{
    /**
     * authorize returns true so request is allowed.
     */
    public function test_authorize_returns_true(): void
    {
        $request = new StoreLayoutRequest;

        $this->assertTrue($request->authorize());
    }

    /**
     * rules contain required name, status, pages, plugins and expected validation constraints.
     */
    public function test_rules_contain_required_name_status_pages_plugins(): void
    {
        $request = new StoreLayoutRequest;
        $rules = $request->rules();

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('status', $rules);
        $this->assertArrayHasKey('pages', $rules);
        $this->assertArrayHasKey('plugins', $rules);
        $nameRules = is_array($rules['name']) ? $rules['name'] : [$rules['name']];
        $this->assertContains('required', $nameRules);
        $this->assertStringContainsString('required', $rules['status']);
    }

    /**
     * attributes returns translated field names for validation messages.
     */
    public function test_attributes_returns_translated_keys(): void
    {
        $request = new StoreLayoutRequest;
        $attributes = $request->attributes();

        $this->assertArrayHasKey('name', $attributes);
        $this->assertArrayHasKey('status', $attributes);
        $this->assertArrayHasKey('pages', $attributes);
        $this->assertArrayHasKey('plugins', $attributes);
    }
}
