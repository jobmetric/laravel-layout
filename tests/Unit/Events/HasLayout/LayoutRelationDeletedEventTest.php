<?php

namespace JobMetric\Layout\Tests\Unit\Events\HasLayout;

use Illuminate\Database\Eloquent\Model;
use JobMetric\Layout\Events\HasLayout\LayoutRelationDeletedEvent;
use JobMetric\Layout\Tests\TestCase;

/**
 * Unit tests for LayoutRelationDeletedEvent (key, definition, constructor payload).
 */
class LayoutRelationDeletedEventTest extends TestCase
{
    /**
     * key returns stable domain event key for layout_relation.deleted.
     */
    public function test_key_returns_stable_string(): void
    {
        $this->assertSame('layout_relation.deleted', LayoutRelationDeletedEvent::key());
    }

    /**
     * definition returns DomainEventDefinition with matching key.
     */
    public function test_definition_returns_domain_event_definition(): void
    {
        $definition = LayoutRelationDeletedEvent::definition();

        $this->assertSame('layout_relation.deleted', $definition->key);
    }

    /**
     * Constructor accepts relatable, application, collection, deletedCount; properties are readable.
     */
    public function test_constructor_accepts_relatable_application_collection_deleted_count(): void
    {
        $relatable = new class extends Model
        {
            protected $table = 'products';
        };
        $relatable->id = 1;

        $event = new LayoutRelationDeletedEvent($relatable, 'shop', 'cat1', 2);

        $this->assertSame($relatable, $event->relatable);
        $this->assertSame('shop', $event->application);
        $this->assertSame('cat1', $event->collection);
        $this->assertSame(2, $event->deletedCount);
    }
}
