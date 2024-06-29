<?php

namespace JobMetric\Layout\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed layout_id
 * @property mixed application
 * @property mixed page
 * @property mixed layout
 */
class LayoutPageResource extends JsonResource
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
            'application' => $this->application,
            'page' => $this->page,

            'layout' => $this->whenLoaded('layout', LayoutResource::make($this->layout)),
        ];
    }
}
