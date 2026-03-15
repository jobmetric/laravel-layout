<?php

namespace JobMetric\Layout\Tests\Unit\Rules;

use JobMetric\Layout\Models\Layout;
use JobMetric\Layout\Rules\CheckExistNameRule;
use JobMetric\Layout\Tests\TestCase;

/**
 * Unit tests for CheckExistNameRule (unique layout name validation, object_id exclusion, trim).
 */
class CheckExistNameRuleTest extends TestCase
{
    /**
     * validate passes when no layout exists with given name; fail callback not called.
     */
    public function test_passes_when_name_does_not_exist(): void
    {
        $rule = new CheckExistNameRule();
        $failed = false;

        $rule->validate('name', 'Unique Name', function () use (&$failed) {
            $failed = true;
        });

        $this->assertFalse($failed);
    }

    /**
     * validate fails when layout with same name exists; fail callback is called.
     */
    public function test_fails_when_name_exists(): void
    {
        Layout::query()->create([
            'name'   => 'Existing Layout',
            'status' => true,
        ]);

        $rule = new CheckExistNameRule();
        $failed = false;

        $rule->validate('name', 'Existing Layout', function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }

    /**
     * validate passes when name exists on same layout (object_id) to allow update without change.
     */
    public function test_passes_when_name_exists_but_is_same_record_with_object_id(): void
    {
        $layout = Layout::query()->create([
            'name'   => 'My Layout',
            'status' => true,
        ]);

        $rule = new CheckExistNameRule($layout->id);
        $failed = false;

        $rule->validate('name', 'My Layout', function () use (&$failed) {
            $failed = true;
        });

        $this->assertFalse($failed);
    }

    /**
     * validate fails when name exists on another layout even when object_id is set.
     */
    public function test_fails_when_name_exists_on_another_record_with_object_id(): void
    {
        Layout::query()->create([
            'name'   => 'Other Layout',
            'status' => true,
        ]);
        $layout = Layout::query()->create([
            'name'   => 'My Layout',
            'status' => true,
        ]);

        $rule = new CheckExistNameRule($layout->id);
        $failed = false;

        $rule->validate('name', 'Other Layout', function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }

    /**
     * validate uses trimmed value so leading/trailing spaces match existing name.
     */
    public function test_trimmed_value_used_for_check(): void
    {
        Layout::query()->create([
            'name'   => 'Trimmed',
            'status' => true,
        ]);

        $rule = new CheckExistNameRule();
        $failed = false;

        $rule->validate('name', '  Trimmed  ', function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }
}
