<?php

namespace JobMetric\Layout\Tests\Unit\Exceptions;

use JobMetric\Layout\Exceptions\CollectionPropertyNotExistException;
use JobMetric\Layout\Tests\TestCase;

/**
 * Unit tests for CollectionPropertyNotExistException (message with field/model, code).
 */
class CollectionPropertyNotExistExceptionTest extends TestCase
{
    /**
     * Constructor sets message containing field and model; default code 500.
     */
    public function test_constructor_sets_message_with_field_and_model(): void
    {
        $e = new CollectionPropertyNotExistException('App\Models\Product', 'category_id');

        $this->assertStringContainsString('category_id', $e->getMessage());
        $this->assertStringContainsString('Product', $e->getMessage());
        $this->assertSame(500, $e->getCode());
    }

    /**
     * Constructor accepts custom exception code.
     */
    public function test_constructor_accepts_custom_code(): void
    {
        $e = new CollectionPropertyNotExistException('App\Models\Post', 'section', 422);

        $this->assertSame(422, $e->getCode());
    }
}
