<?php

namespace JobMetric\Layout\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use JobMetric\Layout\Models\Layout;

/**
 * Class CheckExistNameRule
 *
 * Validates that the layout name is unique in the layouts table.
 * When object_id is given (e.g. on update), that layout is excluded so the same name is allowed for the same record.
 *
 * @package JobMetric\Layout
 */
class CheckExistNameRule implements ValidationRule
{
    /**
     * Layout id to exclude from the uniqueness check (for update); null for create.
     *
     * @var int|null
     */
    private ?int $object_id;

    /**
     * Create a new rule instance.
     *
     * @param int|null $object_id Layout id to exclude when checking uniqueness; null for new layout.
     */
    public function __construct(?int $object_id = null)
    {
        $this->object_id = $object_id;
    }

    /**
     * Run the validation rule.
     *
     * @param string $attribute                                  The validated attribute name.
     * @param mixed $value                                       The provided value to validate.
     * @param Closure(string): PotentiallyTranslatedString $fail Callback to register a failure.
     *
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $name = is_string($value) ? trim($value) : (string) $value;

        if ($name === '') {
            return;
        }

        $query = Layout::query()->where('name', $name);

        if ($this->object_id !== null) {
            $query->where('id', '!=', $this->object_id);
        }

        if ($query->exists()) {
            $fail(trans('layout::base.validation.check_exist_name'));
        }
    }
}
