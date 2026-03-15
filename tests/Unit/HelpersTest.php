<?php

namespace JobMetric\Layout\Tests\Unit;

use JobMetric\Layout\HasLayout;
use JobMetric\Layout\Tests\TestCase;
use stdClass;

/**
 * Unit tests for layout helpers.
 */
class HelpersTest extends TestCase
{
    /**
     * layout_relatable_cache_key builds cache key from model class, id, application and collection.
     */
    public function test_layout_relatable_cache_key_returns_expected_format(): void
    {
        $key = layout_relatable_cache_key('App\Models\Product', 1, 'shop', 'category_1');

        $this->assertSame('layout:relatable:App\Models\Product:1:shop:category_1', $key);
    }

    /**
     * layout_relatable_cache_key uses empty string for null id and null collection.
     */
    public function test_layout_relatable_cache_key_with_null_id_uses_empty_string(): void
    {
        $key = layout_relatable_cache_key('App\Models\Product', null, 'shop', null);

        $this->assertSame('layout:relatable:App\Models\Product::shop:', $key);
    }

    /**
     * layout_relatable_cache_key uses empty string for null application and collection.
     */
    public function test_layout_relatable_cache_key_with_null_application_and_collection(): void
    {
        $key = layout_relatable_cache_key('App\Models\Post', 5, null, null);

        $this->assertSame('layout:relatable:App\Models\Post:5::', $key);
    }

    /**
     * layout_model_uses_has_layout returns true when model instance or class uses HasLayout trait.
     */
    public function test_layout_model_uses_has_layout_returns_true_for_class_using_trait(): void
    {
        $model = new class
        {
            use HasLayout;
        };

        $this->assertTrue(layout_model_uses_has_layout($model));
        $this->assertTrue(layout_model_uses_has_layout(get_class($model)));
    }

    /**
     * layout_model_uses_has_layout returns false for stdClass instance or class.
     */
    public function test_layout_model_uses_has_layout_returns_false_for_std_class(): void
    {
        $this->assertFalse(layout_model_uses_has_layout(new stdClass));
        $this->assertFalse(layout_model_uses_has_layout(stdClass::class));
    }

    /**
     * layout_model_uses_has_layout returns false for non-existent class name.
     */
    public function test_layout_model_uses_has_layout_returns_false_for_non_existent_class(): void
    {
        $this->assertFalse(layout_model_uses_has_layout('NonExistent\Class\Name'));
    }
}
