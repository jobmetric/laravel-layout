<?php

namespace JobMetric\Layout\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed id
 * @property mixed name
 * @property mixed status
 * @property mixed created_at
 * @property mixed updated_at
 * @property mixed layout_plugin_count
 * @property mixed layoutPages
 * @property mixed layoutPlugin
 */
class LayoutResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'layout_plugin_count' => $this->layout_plugin_count,

            'layoutPages' => $this->whenLoaded('layoutPages', LayoutPageResource::collection($this->layoutPages)),
            'layoutPlugin' => $this->whenLoaded('layoutPlugin', LayoutPluginResource::collection($this->layoutPlugin)),
        ];
    }
}
