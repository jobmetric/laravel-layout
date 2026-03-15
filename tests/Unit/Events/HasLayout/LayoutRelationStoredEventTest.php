<?php

namespace JobMetric\Layout\Tests\Unit\Events\HasLayout;

use Illuminate\Database\Eloquent\Model;
use JobMetric\EventSystem\Support\DomainEventDefinition;
use JobMetric\Layout\Events\HasLayout\LayoutRelationStoredEvent;
use JobMetric\Layout\Models\LayoutRelation;
use JobMetric\Layout\Tests\TestCase;

/**
 * Unit tests for LayoutRelationStoredEvent (key, definition, constructor payload).
 */
class LayoutRelationStoredEventTest extends TestCase
{
    /**
     * key returns stable domain event key for layout_relation.stored.
     */
    public function test_key_returns_stable_string(): void
    {
        $this->assertSame('layout_relation.stored', LayoutRelationStoredEvent::key());
    }

    /**
     * definition returns DomainEventDefinition for event registration.
     */
    public function test_definition_returns_domain_event_definition(): void
    {
        $definition = LayoutRelationStoredEvent::definition();

        $this->assertInstanceOf(DomainEventDefinition::class, $definition);
    }

    /**
     * Constructor accepts layoutRelation and relatable model; properties are readable.
     */
    public function test_constructor_accepts_layout_relation_and_relatable(): void
    {
        $relatable = new class extends Model
        {
            protected $table = 'products';
        };
        $relatable->id = 1;
        $relation = new LayoutRelation([
            'layout_id'      => 1,
            'application'    => 'shop',
            'relatable_type' => get_class($relatable),
            'relatable_id'   => 1,
            'collection'     => null,
        ]);

        $event = new LayoutRelationStoredEvent($relation, $relatable);

        $this->assertSame($relation, $event->layoutRelation);
        $this->assertSame($relatable, $event->relatable);
    }
}
