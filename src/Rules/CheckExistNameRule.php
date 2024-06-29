<?php

namespace JobMetric\Layout\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use JobMetric\Layout\Models\Layout;

class CheckExistNameRule implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(
        private int|null $object_id = null
    )
    {
    }

    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = Layout::query()->where('name', $value);

        if ($this->object_id) {
            $query->where('id', '!=', $this->object_id);
        }

        if ($query->exists()) {
            $fail(__('layout::base.validation.check_exist_name'));
        }
    }
}
