<?php

namespace JobMetric\Layout\Events;

use Illuminate\Database\Eloquent\Model;

class LayoutRelationForgetEvent
{
    /**
     * Create a new event instance.
     */
    public function __construct(
        public Model $model
    )
    {
    }
}
