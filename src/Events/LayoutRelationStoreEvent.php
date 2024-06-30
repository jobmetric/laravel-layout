<?php

namespace JobMetric\Layout\Events;

use Illuminate\Database\Eloquent\Model;
use JobMetric\Layout\Models\LayoutRelation;

class LayoutRelationStoreEvent
{
    /**
     * Create a new event instance.
     */
    public function __construct(
        public Model $model,
        public readonly LayoutRelation $layout_relation
    )
    {
    }
}
