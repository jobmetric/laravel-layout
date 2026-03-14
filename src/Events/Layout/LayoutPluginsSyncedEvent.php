<?php

namespace JobMetric\Layout\Events\Layout;

use JobMetric\EventSystem\Contracts\DomainEvent;
use JobMetric\EventSystem\Support\DomainEventDefinition;
use JobMetric\Layout\Models\Layout;

/**
 * Domain event when layout plugins are synced.
 *
 * @package JobMetric\Layout
 */
readonly class LayoutPluginsSyncedEvent implements DomainEvent
{
    /**
     * Create a new event instance.
     */
    public function __construct(
        public Layout $layout,
        public array $plugins
    ) {
    }

    /**
     * Returns the stable technical key for the domain event.
     *
     * @return string
     */
    public static function key(): string
    {
        return 'layout.plugins_synced';
    }

    /**
     * Returns the full metadata definition for this domain event.
     *
     * @return DomainEventDefinition
     */
    public static function definition(): DomainEventDefinition
    {
        return new DomainEventDefinition(
            self::key(),
            'layout::base.events.group',
            'layout::base.events.layout_plugins_synced.title',
            'layout::base.events.layout_plugins_synced.description',
            'fas fa-puzzle-piece',
            ['layout', 'storage', 'management']
        );
    }
}
