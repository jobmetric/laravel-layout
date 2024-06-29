<?php

namespace JobMetric\Layout\Events\Layout;

use JobMetric\Layout\Models\Layout;

class LayoutStoreEvent
{
    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly Layout $layout,
        public readonly array  $data
    )
    {
    }
}
