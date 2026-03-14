<?php

namespace JobMetric\Layout\Events\Layout;

use JobMetric\EventSystem\Contracts\DomainEvent;
use JobMetric\EventSystem\Support\DomainEventDefinition;
use JobMetric\Layout\Models\Layout;

/**
 * Domain event when layout pages are synced.
 *
 * @package JobMetric\Layout
 */
readonly class LayoutPagesSyncedEvent implements DomainEvent
{
    /**
     * Create a new event instance.
     */
    public function __construct(
        public Layout $layout,
        public array $pages
    ) {
    }

    /**
     * Returns the stable technical key for the domain event.
     *
     * @return string
     */
    public static function key(): string
    {
        return 'layout.pages_synced';
    }

    /**
     * Returns the full metadata definition for this domain event.
     *
     * @return DomainEventDefinition
     */
    public static function definition(): DomainEventDefinition
    {
        return new DomainEventDefinition(self::key(), 'layout::base.events.group', 'layout::base.events.layout_pages_synced.title', 'layout::base.events.layout_pages_synced.description', 'fas fa-list', [
                'layout',
                'storage',
                'management',
            ]);
    }
}
