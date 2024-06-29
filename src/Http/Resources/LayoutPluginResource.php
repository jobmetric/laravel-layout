<?php

namespace JobMetric\Layout\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JobMetric\Extension\Http\Resources\PluginResource;

/**
 * @property mixed layout_id
 * @property mixed plugin_id
 * @property mixed position
 * @property mixed ordering
 * @property mixed layout
 * @property mixed plugin
 */
class LayoutPluginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'layout_id' => $this->layout_id,
            'plugin_id' => $this->plugin_id,
            'position' => $this->position,
            'ordering' => $this->ordering,

            'layout' => $this->whenLoaded('layout', LayoutResource::make($this->layout)),
            'plugin' => $this->whenLoaded('plugin', PluginResource::make($this->plugin)),
        ];
    }
}
