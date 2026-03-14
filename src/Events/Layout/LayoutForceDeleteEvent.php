<?php

namespace JobMetric\Layout\Events\Layout;

use JobMetric\EventSystem\Contracts\DomainEvent;
use JobMetric\EventSystem\Support\DomainEventDefinition;
use JobMetric\Layout\Models\Layout;

/**
 * Domain event when a layout is force deleted.
 *
 * @package JobMetric\Layout
 */
readonly class LayoutForceDeleteEvent implements DomainEvent
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
        return 'layout.force_deleted';
    }

    /**
     * Returns the full metadata definition for this domain event.
     *
     * @return DomainEventDefinition
     */
    public static function definition(): DomainEventDefinition
    {
        return new DomainEventDefinition(self::key(), 'layout::base.events.group', 'layout::base.events.layout_force_deleted.title', 'layout::base.events.layout_force_deleted.description', 'fas fa-trash-alt', [
                'layout',
                'storage',
                'management',
            ]);
    }
}
