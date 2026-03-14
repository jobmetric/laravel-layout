<?php

namespace JobMetric\Layout\Events\Layout;

use JobMetric\EventSystem\Contracts\DomainEvent;
use JobMetric\EventSystem\Support\DomainEventDefinition;
use JobMetric\Layout\Models\Layout;

/**
 * Domain event when a layout is restored.
 *
 * @package JobMetric\Layout
 */
readonly class LayoutRestoreEvent implements DomainEvent
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
        return 'layout.restored';
    }

    /**
     * Returns the full metadata definition for this domain event.
     *
     * @return DomainEventDefinition
     */
    public static function definition(): DomainEventDefinition
    {
        return new DomainEventDefinition(self::key(), 'layout::base.events.group', 'layout::base.events.layout_restored.title', 'layout::base.events.layout_restored.description', 'fas fa-undo', [
                'layout',
                'storage',
                'management',
            ]);
    }
}
