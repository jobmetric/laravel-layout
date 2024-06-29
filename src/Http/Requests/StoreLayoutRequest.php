<?php

namespace JobMetric\Layout\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use JobMetric\Extension\Models\Plugin;
use JobMetric\Layout\Rules\CheckExistNameRule;

class StoreLayoutRequest extends FormRequest
{
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
        return [
            'name' => [
                'required',
                'string',
                new CheckExistNameRule
            ],
            'status' => 'required|boolean',

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
}
