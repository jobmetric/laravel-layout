<?php

namespace JobMetric\Layout\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use JobMetric\Extension\Models\Plugin;
use JobMetric\Layout\Rules\CheckExistNameRule;

class UpdateLayoutRequest extends FormRequest
{
    public int|null $layout_id = null;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        if (is_null($this->layout_id)) {
            $layout_id = $this->route()->parameter('layout')->id;
        } else {
            $layout_id = $this->layout_id;
        }

        return [
            'name' => [
                'string',
                'sometimes',
                new CheckExistNameRule($layout_id)
            ],
            'status' => 'boolean|sometimes',

            'pages' => 'required|array',
            'pages.*.application' => 'string',
            'pages.*.page' => 'string',

            'plugins' => 'required|array',
            'plugins.*.plugin_id' => [
                'integer',
                'exists:' . (new Plugin)->getTable() . ',id'
            ],
            'plugins.*.position' => 'string',
            'plugins.*.ordering' => 'integer|nullable',
        ];
    }

    /**
     * Set layout id for validation
     *
     * @param int $layout_id
     * @return static
     */
    public function setLayoutId(int $layout_id): static
    {
        $this->layout_id = $layout_id;

        return $this;
    }
}
