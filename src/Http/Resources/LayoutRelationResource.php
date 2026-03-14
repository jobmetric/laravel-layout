<?php

namespace JobMetric\Layout\Http\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JobMetric\Layout\Models\Layout;

/**
 * Class LayoutRelationResource
 *
 * Transforms the LayoutRelation model into a structured JSON resource.
 *
 * @property string|null $application
 * @property string $relatable_type
 * @property int $relatable_id
 * @property int $layout_id
 * @property string|null $collection
 *
 * @property-read Model $relatable
 * @property-read Layout $layout
 */
class LayoutRelationResource extends JsonResource
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
            'application'    => $this->application,
            'relatable_type' => $this->relatable_type,
            'relatable_id'   => $this->relatable_id,
            'layout_id'      => $this->layout_id,
            'collection'     => $this->collection,

            'layout' => $this->whenLoaded('layout', function () {
                return LayoutResource::make($this->layout);
            }),

            'relatable' => $this->whenLoaded('relatable', function () {
                return $this->relatable;
            }),
        ];
    }
}
