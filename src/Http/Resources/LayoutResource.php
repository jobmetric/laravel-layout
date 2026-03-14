<?php

namespace JobMetric\Layout\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use JobMetric\Layout\Models\LayoutPage;
use JobMetric\Layout\Models\LayoutPlugin;
use JobMetric\Layout\Models\LayoutRelation;

/**
 * Class LayoutResource
 *
 * Transforms the Layout model into a structured JSON resource.
 *
 * @property int $id
 * @property string $name
 * @property bool $status
 * @property Carbon|null $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read int $layout_plugin_count
 * @property-read LayoutPage[] $layoutPages
 * @property-read LayoutPlugin[] $layoutPlugins
 * @property-read LayoutRelation[] $layoutRelations
 */
class LayoutResource extends JsonResource
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
            'id'     => $this->id,
            'name'   => $this->name,
            'status' => (bool) $this->status,

            'deleted_at' => $this->deleted_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            'layout_plugin_count' => $this->whenLoaded('layoutPlugins', function () {
                return $this->layout_plugin_count;
            }),

            'layout_pages' => $this->whenLoaded('layoutPages', function () {
                return LayoutPageResource::collection($this->layoutPages);
            }),

            'layout_plugins' => $this->whenLoaded('layoutPlugins', function () {
                return LayoutPluginResource::collection($this->layoutPlugins);
            }),

            'layout_relations' => $this->whenLoaded('layoutRelations', function () {
                return LayoutRelationResource::collection($this->layoutRelations);
            }),
        ];
    }
}
