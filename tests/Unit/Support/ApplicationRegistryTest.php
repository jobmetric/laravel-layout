<?php

namespace JobMetric\Layout\Tests\Unit\Support;

use InvalidArgumentException;
use JobMetric\Layout\Support\ApplicationRegistry;
use JobMetric\Layout\Tests\TestCase;

/**
 * Unit tests for ApplicationRegistry.
 */
class ApplicationRegistryTest extends TestCase
{
    protected ApplicationRegistry $registry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry = new ApplicationRegistry();
    }

    /**
     * register accepts key with valid English chars (a-z, 0-9, _) and stores options; has/get return them.
     */
    public function test_register_accepts_valid_english_key(): void
    {
        $this->registry->register('shop', [
            'name'  => 'Shop',
            'order' => 1,
        ]);

        $this->assertTrue($this->registry->has('shop'));
        $this->assertSame([
            'name'  => 'Shop',
            'order' => 1,
        ], $this->registry->get('shop'));
    }

    /**
     * register throws InvalidArgumentException when key contains invalid chars (e.g. dash).
     */
    public function test_register_throws_for_key_with_dash(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('English only');

        $this->registry->register('my-app', []);
    }

    /**
     * register throws when key contains space.
     */
    public function test_register_throws_for_key_with_space(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->registry->register('my app', []);
    }

    /**
     * register normalizes key to lowercase so has/get find it by normalized key.
     */
    public function test_register_normalizes_key_to_lowercase(): void
    {
        $this->registry->register('SHOP', [
            'name' => 'Shop',
        ]);

        $this->assertTrue($this->registry->has('shop'));
        $this->assertNotNull($this->registry->get('shop'));
    }

    /**
     * unregister removes application from registry; has returns false afterward.
     */
    public function test_unregister_removes_application(): void
    {
        $this->registry->register('shop', []);
        $this->assertTrue($this->registry->has('shop'));

        $this->registry->unregister('shop');
        $this->assertFalse($this->registry->has('shop'));
    }

    /**
     * getOption returns value for given option key (name, order, default_layout).
     */
    public function test_getOption_returns_option_value(): void
    {
        $this->registry->register('cms', [
            'name'           => 'CMS',
            'order'          => 2,
            'default_layout' => 5,
        ]);

        $this->assertSame('CMS', $this->registry->getOption('cms', 'name'));
        $this->assertSame(2, $this->registry->getOption('cms', 'order'));
        $this->assertSame(5, $this->registry->getOption('cms', 'default_layout'));
    }

    /**
     * getOption returns default when option key is missing.
     */
    public function test_getOption_returns_default_when_option_missing(): void
    {
        $this->registry->register('shop', [
            'name' => 'Shop',
        ]);

        $this->assertNull($this->registry->getOption('shop', 'missing'));
        $this->assertSame(99, $this->registry->getOption('shop', 'missing', 99));
    }

    /**
     * getDefaultLayout returns null when application has no default_layout option.
     */
    public function test_getDefaultLayout_returns_null_when_not_set(): void
    {
        $this->registry->register('shop', [
            'name' => 'Shop',
        ]);

        $this->assertNull($this->registry->getDefaultLayout('shop'));
    }

    /**
     * getDefaultLayout returns layout id when application has default_layout option.
     */
    public function test_getDefaultLayout_returns_id_when_set(): void
    {
        $this->registry->register('shop', [
            'default_layout' => 3,
        ]);

        $this->assertSame(3, $this->registry->getDefaultLayout('shop'));
    }

    /**
     * getDefaultLayout returns null for unregistered application key.
     */
    public function test_getDefaultLayout_returns_null_for_unregistered_application(): void
    {
        $this->assertNull($this->registry->getDefaultLayout('unknown'));
    }

    /**
     * values returns application keys sorted by order option then by registration order.
     */
    public function test_values_returns_keys_sorted_by_order_then_registration(): void
    {
        $this->registry->register('first', [
            'order' => 10,
        ]);
        $this->registry->register('second', [
            'order' => 5,
        ]);
        $this->registry->register('third', [
            'order' => 5,
        ]);

        $values = $this->registry->values();

        $this->assertSame('second', $values[0]);
        $this->assertSame('third', $values[1]);
        $this->assertSame('first', $values[2]);
    }

    /**
     * all returns all registered applications as key => options array.
     */
    public function test_all_returns_all_applications_with_options(): void
    {
        $this->registry->register('shop', [
            'name' => 'Shop',
        ]);
        $this->registry->register('cms', [
            'name' => 'CMS',
        ]);

        $all = $this->registry->all();

        $this->assertCount(2, $all);
        $this->assertArrayHasKey('shop', $all);
        $this->assertArrayHasKey('cms', $all);
    }

    /**
     * allTranslated returns list with key, name, description (via trans), order and options.
     */
    public function test_allTranslated_returns_array_with_translated_name_and_description(): void
    {
        $this->registry->register('shop', [
            'name'        => 'layout::base.entity_names.layout',
            'description' => '',
        ]);

        $result = $this->registry->allTranslated();

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('key', $result[0]);
        $this->assertArrayHasKey('name', $result[0]);
        $this->assertArrayHasKey('description', $result[0]);
        $this->assertArrayHasKey('order', $result[0]);
        $this->assertSame('shop', $result[0]['key']);
    }

    /**
     * clear removes all applications from registry; values returns empty array.
     */
    public function test_clear_removes_all_applications(): void
    {
        $this->registry->register('shop', []);
        $this->registry->register('cms', []);

        $this->registry->clear();

        $this->assertFalse($this->registry->has('shop'));
        $this->assertFalse($this->registry->has('cms'));
        $this->assertSame([], $this->registry->values());
    }
}
