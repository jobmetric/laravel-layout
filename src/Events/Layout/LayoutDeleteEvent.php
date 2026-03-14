<?php

namespace JobMetric\Layout\Events\Layout;

use JobMetric\EventSystem\Contracts\DomainEvent;
use JobMetric\EventSystem\Support\DomainEventDefinition;
use JobMetric\Layout\Models\Layout;

/**
 * Domain event when a layout is deleted.
 *
 * @package JobMetric\Layout
 */
readonly class LayoutDeleteEvent implements DomainEvent
{
    /**
     * Create a new event instance.
     */
    public function __construct(
        public Layout $layout
    ) {
    }

    /**
     * Returns the stable technical key for the domain event.
     *
     * @return string
     */
    public static function key(): string
    {
        return 'layout.deleted';
    }

    /**
     * Returns the full metadata definition for this domain event.
     *
     * @return DomainEventDefinition
     */
    public static function definition(): DomainEventDefinition
    {
        return new DomainEventDefinition(self::key(), 'layout::base.events.group', 'layout::base.events.layout_deleted.title', 'layout::base.events.layout_deleted.description', 'fas fa-trash-alt', [
                'layout',
                'storage',
                'management',
            ]);
    }
}
