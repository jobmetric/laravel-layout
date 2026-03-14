<?php

namespace JobMetric\Layout\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JobMetric\Layout\Rules\CheckExistNameRule;

/**
 * Class StoreLayoutRequest
 *
 * Validation rules for creating a new layout (name, status, pages, plugins).
 *
 * @package JobMetric\Layout
 */
class StoreLayoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $pluginTable = config('extension.tables.plugin', 'plugins');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                new CheckExistNameRule(),
            ],
            'status' => 'required|boolean',

            'pages' => 'required|array',
            'pages.*.application' => 'nullable|string|max:100',
            'pages.*.page' => 'nullable|string|max:100',

            'plugins' => 'required|array',
            'plugins.*.plugin_id' => 'required|integer|exists:' . $pluginTable . ',id',
            'plugins.*.position' => 'required|string|max:255',
            'plugins.*.ordering' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => trans('layout::base.fields.name'),
            'status' => trans('layout::base.fields.status'),
            'pages' => trans('layout::base.fields.pages'),
            'pages.*.application' => trans('layout::base.fields.application'),
            'pages.*.page' => trans('layout::base.fields.page'),
            'plugins' => trans('layout::base.fields.plugins'),
            'plugins.*.plugin_id' => trans('layout::base.fields.plugin_id'),
            'plugins.*.position' => trans('layout::base.fields.position'),
            'plugins.*.ordering' => trans('layout::base.fields.ordering'),
        ];
    }
}
