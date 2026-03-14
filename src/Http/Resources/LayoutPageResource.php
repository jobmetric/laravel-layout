<?php

namespace JobMetric\Layout\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JobMetric\Layout\Models\Layout;

/**
 * Class LayoutPageResource
 *
 * Transforms the LayoutPage model into a structured JSON resource.
 *
 * @property int $layout_id
 * @property string|null $application
 * @property string|null $page
 *
 * @property-read Layout $layout
 */
class LayoutPageResource extends JsonResource
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
            'layout_id'   => $this->layout_id,
            'application' => $this->application,
            'page'        => $this->page,

            'layout' => $this->whenLoaded('layout', function () {
                return LayoutResource::make($this->layout);
            }),
        ];
    }
}
