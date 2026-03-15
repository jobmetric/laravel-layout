<?php

namespace JobMetric\Layout\Tests\Unit\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use JobMetric\Layout\Models\Layout;
use JobMetric\Layout\Models\LayoutRelation;
use JobMetric\Layout\Tests\TestCase;

/**
 * Unit tests for LayoutRelation model (table, fillable, timestamps off, layout/relatable relations, scope).
 */
class LayoutRelationModelTest extends TestCase
{
    /**
     * getTable returns config layout_relations table name.
     */
    public function test_getTable_returns_config_value(): void
    {
        $this->assertSame('layout_relations', (new LayoutRelation)->getTable());
    }

    /**
     * fillable contains application, relatable_type, relatable_id, layout_id, collection.
     */
    public function test_fillable_contains_expected_attributes(): void
    {
        $fillable = (new LayoutRelation)->getFillable();

        $this->assertContains('application', $fillable);
        $this->assertContains('relatable_type', $fillable);
        $this->assertContains('relatable_id', $fillable);
        $this->assertContains('layout_id', $fillable);
        $this->assertContains('collection', $fillable);
    }

    /**
     * LayoutRelation has timestamps disabled.
     */
    public function test_timestamps_disabled(): void
    {
        $relation = new LayoutRelation;

        $this->assertFalse($relation->timestamps);
    }

    /**
     * layout relation returns BelongsTo Layout.
     */
    public function test_layout_relation_returns_belongs_to(): void
    {
        $layoutRelation = new LayoutRelation;
        $relation = $layoutRelation->layout();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertSame(Layout::class, $relation->getRelated()::class);
    }

    /**
     * relatable relation returns MorphTo for polymorphic relatable model.
     */
    public function test_relatable_relation_returns_morph_to(): void
    {
        $layoutRelation = new LayoutRelation;

        $this->assertInstanceOf(MorphTo::class, $layoutRelation->relatable());
    }

    /**
     * scopeApplicationCollection filters by application and collection.
     */
    public function test_scopeApplicationCollection_filters_correctly(): void
    {
        $layout = Layout::query()->create([
            'name'   => 'L',
            'status' => true,
        ]);

        LayoutRelation::query()->create([
            'application'    => 'shop',
            'relatable_type' => 'App\Models\Product',
            'relatable_id'   => 1,
            'layout_id'      => $layout->id,
            'collection'     => 'cat1',
        ]);

        $found = LayoutRelation::query()->applicationCollection('shop', 'cat1')->first();

        $this->assertNotNull($found);
        $this->assertSame('cat1', $found->collection);
    }
}
