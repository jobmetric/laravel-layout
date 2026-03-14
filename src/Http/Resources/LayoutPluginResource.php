<?php

namespace JobMetric\Layout\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JobMetric\Extension\Http\Resources\PluginResource;
use JobMetric\Extension\Models\Plugin;
use JobMetric\Layout\Models\Layout;

/**
 * Class LayoutPluginResource
 *
 * Transforms the LayoutPlugin model into a structured JSON resource.
 *
 * @property int $layout_id
 * @property int $plugin_id
 * @property string $position
 * @property int $ordering
 *
 * @property-read Layout $layout
 * @property-read Plugin $plugin
 */
class LayoutPluginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'layout_id' => $this->layout_id,
            'plugin_id' => $this->plugin_id,
            'position'  => $this->position,
            'ordering'  => (int) $this->ordering,

            'layout' => $this->whenLoaded('layout', function () {
                return LayoutResource::make($this->layout);
            }),

            'plugin' => $this->whenLoaded('plugin', function () {
                return PluginResource::make($this->plugin);
            }),
        ];
    }
}
