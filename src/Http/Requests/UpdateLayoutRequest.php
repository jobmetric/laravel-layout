<?php

namespace JobMetric\Layout\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JobMetric\Layout\Rules\CheckExistNameRule;

/**
 * Class UpdateLayoutRequest
 *
 * Validation rules for updating an existing layout (name, status, pages, plugins).
 * Layout id can be provided via route parameter, context, or input.
 *
 * @package JobMetric\Layout
 */
class UpdateLayoutRequest extends FormRequest
{
    /**
     * External context (injected via setContext or setLayoutId).
     *
     * @var array<string, mixed>
     */
    protected array $context = [];

    /**
     * Set context for validation (e.g. layout_id when not from route).
     *
     * @param array<string, mixed> $context
     *
     * @return void
     */
    public function setContext(array $context): void
    {
        $this->context = $context;
    }

    /**
     * Set layout id for validation (convenience for setContext(['layout_id' => $id])).
     *
     * @param int $layout_id
     *
     * @return static
     */
    public function setLayoutId(int $layout_id): static
    {
        $this->setContext(['layout_id' => $layout_id]);

        return $this;
    }

    /**
     * Build validation rules for the given input and context.
     *
     * @param array<string, mixed> $input
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    public static function rulesFor(array $input, array $context = []): array
    {
        $layoutId = (int) ($context['layout_id'] ?? $input['layout_id'] ?? 0);
        $pluginTable = config('extension.tables.plugin', 'plugins');

        return [
            'name'   => [
                'sometimes',
                'string',
                'max:255',
                new CheckExistNameRule($layoutId),
            ],
            'status' => 'sometimes|boolean',

            'pages'               => 'sometimes|array',
            'pages.*.application' => 'nullable|string|max:100',
            'pages.*.page'        => 'nullable|string|max:100',

            'plugins'             => 'sometimes|array',
            'plugins.*.plugin_id' => 'required_with:plugins|integer|exists:' . $pluginTable . ',id',
            'plugins.*.position'  => 'required_with:plugins|string|max:255',
            'plugins.*.ordering'  => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $layoutId = (int) ($this->context['layout_id'] ?? $this->route('layout')?->id ?? $this->input('layout_id') ?? 0);

        return self::rulesFor($this->all(), [
            'layout_id' => $layoutId,
        ]);
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name'                => trans('layout::base.fields.name'),
            'status'              => trans('layout::base.fields.status'),
            'pages'               => trans('layout::base.fields.pages'),
            'pages.*.application' => trans('layout::base.fields.application'),
            'pages.*.page'        => trans('layout::base.fields.page'),
            'plugins'             => trans('layout::base.fields.plugins'),
            'plugins.*.plugin_id' => trans('layout::base.fields.plugin_id'),
            'plugins.*.position'  => trans('layout::base.fields.position'),
            'plugins.*.ordering'  => trans('layout::base.fields.ordering'),
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }
}
