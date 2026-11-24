<?php

namespace JobMetric\Layout\Events\LayoutPlugins;

use JobMetric\EventSystem\Contracts\DomainEvent;
use JobMetric\EventSystem\Support\DomainEventDefinition;

/**
 * Describe a committed LayoutPlugins synchronized outcome using allowlisted identifiers.
 */
readonly class LayoutPluginsSyncCommittedEvent implements DomainEvent
{
    /**
     * Capture identifiers without models, input values or credentials.
     *
     * @param int|string $id
     *
     * @return void
     */
    public function __construct(
        public int|string $id
    ) {
    }

    /**
     * Return the stable key used by automation consumers.
     *
     * @return string
     */
    public static function key(): string
    {
        return 'layout.layout_plugins.synced.committed';
    }

    /**
     * Describe the outcome for translated discovery interfaces.
     *
     * @return DomainEventDefinition
     */
    public static function definition(): DomainEventDefinition
    {
        return new DomainEventDefinition(self::key(), 'layout::automation.layout_plugins_synced.group', 'layout::automation.layout_plugins_synced.title', 'layout::automation.layout_plugins_synced.description', 'fas fa-bolt', ['automation', 'committed', 'layout']);
    }
}
