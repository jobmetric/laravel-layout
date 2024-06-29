<?php

namespace JobMetric\Layout\Events;

use JobMetric\Layout\Models\Layout;

class LayoutRestoreEvent
{
    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly Layout $layout,
    )
    {
    }
}
