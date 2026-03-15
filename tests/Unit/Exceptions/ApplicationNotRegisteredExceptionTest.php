<?php

namespace JobMetric\Layout\Tests\Unit\Exceptions;

use JobMetric\Layout\Exceptions\ApplicationNotRegisteredException;
use JobMetric\Layout\Tests\TestCase;

/**
 * Unit tests for ApplicationNotRegisteredException (message with application key, code).
 */
class ApplicationNotRegisteredExceptionTest extends TestCase
{
    /**
     * Constructor sets message containing application key and default code 422.
     */
    public function test_constructor_sets_message_with_application(): void
    {
        $e = new ApplicationNotRegisteredException('unknown_app');

        $this->assertStringContainsString('unknown_app', $e->getMessage());
        $this->assertSame(422, $e->getCode());
    }

    /**
     * Constructor accepts custom exception code.
     */
    public function test_constructor_accepts_custom_code(): void
    {
        $e = new ApplicationNotRegisteredException('shop', 400);

        $this->assertSame(400, $e->getCode());
    }
}
