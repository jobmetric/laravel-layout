<?php

namespace JobMetric\Layout\Tests\Unit\Http\Requests;

use JobMetric\Layout\Http\Requests\UpdateLayoutRequest;
use JobMetric\Layout\Tests\TestCase;

/**
 * Unit tests for UpdateLayoutRequest (authorize, context, setLayoutId, rulesFor, attributes).
 */
class UpdateLayoutRequestTest extends TestCase
{
    /**
     * authorize returns true so request is allowed.
     */
    public function test_authorize_returns_true(): void
    {
        $request = new UpdateLayoutRequest;

        $this->assertTrue($request->authorize());
    }

    /**
     * setContext sets internal context so rules use sometimes for optional fields.
     */
    public function test_setContext_sets_internal_context(): void
    {
        $request = new UpdateLayoutRequest;
        $request->setContext(['layout_id' => 5]);

        $rules = $request->rules();

        $this->assertArrayHasKey('name', $rules);
        $this->assertContains('sometimes', $rules['name']);
    }

    /**
     * setLayoutId sets layout_id in context and returns self for chaining.
     */
    public function test_setLayoutId_sets_context_and_returns_self(): void
    {
        $request = new UpdateLayoutRequest;
        $result = $request->setLayoutId(10);

        $this->assertSame($request, $result);
    }

    /**
     * rulesFor returns validation rules with sometimes for update context.
     */
    public function test_rulesFor_returns_sometimes_rules_for_name_status(): void
    {
        $rules = UpdateLayoutRequest::rulesFor([], [
            'layout_id' => 1,
        ]);

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('status', $rules);
        $this->assertContains('sometimes', $rules['name']);
    }

    /**
     * attributes returns translated field names for validation messages.
     */
    public function test_attributes_returns_translated_keys(): void
    {
        $request = new UpdateLayoutRequest;
        $attributes = $request->attributes();

        $this->assertArrayHasKey('name', $attributes);
        $this->assertArrayHasKey('plugins', $attributes);
    }
}
