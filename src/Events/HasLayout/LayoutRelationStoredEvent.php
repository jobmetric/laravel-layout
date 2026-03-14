<?php

namespace JobMetric\Layout\Events\HasLayout;

use Illuminate\Database\Eloquent\Model;
use JobMetric\EventSystem\Contracts\DomainEvent;
use JobMetric\EventSystem\Support\DomainEventDefinition;
use JobMetric\Layout\Models\LayoutRelation;

/**
 * Fired when a layout is attached to a model via storeLayout().
 *
 * @package JobMetric\Layout
 */
readonly class LayoutRelationStoredEvent implements DomainEvent
{
    /**
     * Create a new event instance.
     */
    public function __construct(
        public LayoutRelation $layoutRelation,
        public Model $relatable,
    ) {}

    /**
     * Returns the stable technical key for the domain event.
     *
     * @return string
     */
    public static function key(): string
    {
        return 'layout_relation.stored';
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
            'layout::base.events.layout_relation_stored.title',
            'layout::base.events.layout_relation_stored.description',
            'fas fa-link',
            ['layout', 'has_layout', 'relation']
        );
    }
}
