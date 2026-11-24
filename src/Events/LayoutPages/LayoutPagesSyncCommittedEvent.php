<?php

namespace JobMetric\Layout\Events\LayoutPages;

use JobMetric\EventSystem\Contracts\DomainEvent;
use JobMetric\EventSystem\Support\DomainEventDefinition;

/**
 * Describe a committed LayoutPages synchronized outcome using allowlisted identifiers.
 */
readonly class LayoutPagesSyncCommittedEvent implements DomainEvent
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
        return 'layout.layout_pages.synced.committed';
    }

    /**
     * Describe the outcome for translated discovery interfaces.
     *
     * @return DomainEventDefinition
     */
    public static function definition(): DomainEventDefinition
    {
        return new DomainEventDefinition(self::key(), 'layout::automation.layout_pages_synced.group', 'layout::automation.layout_pages_synced.title', 'layout::automation.layout_pages_synced.description', 'fas fa-bolt', ['automation', 'committed', 'layout']);
    }
}
