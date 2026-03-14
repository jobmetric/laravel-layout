<?php

namespace JobMetric\Layout\Events\HasLayout;

use Illuminate\Database\Eloquent\Model;
use JobMetric\EventSystem\Contracts\DomainEvent;
use JobMetric\EventSystem\Support\DomainEventDefinition;

/**
 * Fired when layout bindings are removed from a model via forgetLayout().
 *
 * @package JobMetric\Layout
 */
readonly class LayoutRelationDeletedEvent implements DomainEvent
{
    /**
     * Create a new event instance.
     */
    public function __construct(
        public Model $relatable,
        public string $application,
        public ?string $collection,
        public int $deletedCount,
    ) {}

    /**
     * Returns the stable technical key for the domain event.
     *
     * @return string
     */
    public static function key(): string
    {
        return 'layout_relation.deleted';
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
            'layout::base.events.layout_relation_deleted.title',
            'layout::base.events.layout_relation_deleted.description',
            'fas fa-unlink',
            ['layout', 'has_layout', 'relation']
        );
    }
}
