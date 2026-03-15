<?php

namespace JobMetric\Layout\Tests\Feature;

use Illuminate\Database\Eloquent\Model;
use JobMetric\Layout\Exceptions\ApplicationNotRegisteredException;
use JobMetric\Layout\Exceptions\CollectionPropertyNotExistException;
use JobMetric\Layout\Facades\ApplicationRegistry;
use JobMetric\Layout\HasLayout;
use JobMetric\Layout\Models\Layout;
use JobMetric\Layout\Models\LayoutPage;
use JobMetric\Layout\Models\LayoutRelation;
use JobMetric\Layout\Tests\Stubs\Models\LayoutTestProduct;
use JobMetric\Layout\Tests\TestCase;

/**
 * Feature tests for HasLayout trait (storeLayout, getLayout, forgetLayout, scopes, etc.).
 * Mirrors Translation/Metadata style: assertDatabaseHas/Missing for persistence, Stub model + factory.
 */
class HasLayoutTraitTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->assertTrue($this->app['db']->connection()
            ->getSchemaBuilder()
            ->hasTable(config('layout.tables.layout_relation')), 'Layout relation table must exist via provider migration.');

        ApplicationRegistry::register('shop', [
            'name'           => 'Shop',
            'order'          => 1,
            'default_layout' => null,
        ]);
    }

    protected function tearDown(): void
    {
        ApplicationRegistry::clear();
        parent::tearDown();
    }

    /**
     * Store then forget: assert DB state like HasMetadataTest / HasTranslationTest.
     *
     * @throws ApplicationNotRegisteredException
     */
    public function test_store_layout_and_forget_database_state(): void
    {
        $layout = Layout::query()->create([
            'name'   => 'L',
            'status' => true,
        ]);
        $product = LayoutTestProduct::factory()->create();

        $product->storeLayout($layout, 'shop', null);

        $this->assertDatabaseHas(config('layout.tables.layout_relation'), [
            'relatable_type' => LayoutTestProduct::class,
            'relatable_id'   => $product->id,
            'application'    => 'shop',
            'layout_id'      => $layout->id,
            'collection'     => null,
        ]);

        $product->forgetLayout('shop', null);

        $this->assertDatabaseMissing(config('layout.tables.layout_relation'), [
            'relatable_type' => LayoutTestProduct::class,
            'relatable_id'   => $product->id,
            'application'    => 'shop',
            'layout_id'      => $layout->id,
        ]);
    }

    /**
     * storeLayout creates layout_relation record and returns LayoutRelation; DB has row.
     *
     * @throws ApplicationNotRegisteredException
     */
    public function test_storeLayout_creates_relation_and_fires_event(): void
    {
        $layout = Layout::query()->create([
            'name'   => 'L',
            'status' => true,
        ]);
        $product = LayoutTestProduct::factory()->create();

        $relation = $product->storeLayout($layout, 'shop', null);

        $this->assertInstanceOf(LayoutRelation::class, $relation);
        $this->assertSame($layout->id, $relation->layout_id);
        $this->assertSame('shop', $relation->application);

        $this->assertDatabaseHas(config('layout.tables.layout_relation'), [
            'relatable_type' => LayoutTestProduct::class,
            'relatable_id'   => $product->id,
            'application'    => 'shop',
            'layout_id'      => $layout->id,
        ]);
    }

    /**
     * storeLayout throws ApplicationNotRegisteredException when application key not in registry.
     */
    public function test_storeLayout_throws_when_application_not_registered(): void
    {
        $layout = Layout::query()->create([
            'name'   => 'L',
            'status' => true,
        ]);
        $product = LayoutTestProduct::factory()->create();

        $this->expectException(ApplicationNotRegisteredException::class);

        $product->storeLayout($layout, 'unknown_app', null);
    }

    /**
     * getLayout returns Layout model when model has stored layout for application/collection.
     *
     * @throws ApplicationNotRegisteredException
     * @throws CollectionPropertyNotExistException
     */
    public function test_getLayout_returns_layout_from_relation(): void
    {
        $layout = Layout::query()->create([
            'name'   => 'L',
            'status' => true,
        ]);
        $product = LayoutTestProduct::factory()->create();
        $product->storeLayout($layout, 'shop', null);

        $resolved = $product->getLayout('shop', null);

        $this->assertInstanceOf(Layout::class, $resolved);
        $this->assertSame($layout->id, $resolved->id);
    }

    /**
     * getLayout returns null when no relation and no layout page fallback.
     *
     * @throws CollectionPropertyNotExistException
     */
    public function test_getLayout_returns_null_when_no_relation_and_no_fallback(): void
    {
        $product = LayoutTestProduct::factory()->create();

        $this->assertNull($product->getLayout('shop', null));
    }

    /**
     * getLayout falls back to layout from layout_pages (application/page) when no direct relation.
     *
     * @throws CollectionPropertyNotExistException
     */
    public function test_getLayout_falls_back_to_layout_page_when_configured(): void
    {
        $layout = Layout::query()->create([
            'name'   => 'L',
            'status' => true,
        ]);
        LayoutPage::query()->create([
            'layout_id'   => $layout->id,
            'application' => 'shop',
            'page'        => 'product',
        ]);
        $product = LayoutTestProduct::factory()->create();

        $resolved = $product->getLayout('shop', null);

        $this->assertInstanceOf(Layout::class, $resolved);
        $this->assertSame($layout->id, $resolved->id);
    }

    /**
     * forgetLayout removes layout_relation rows for application/collection; DB missing row.
     *
     * @throws ApplicationNotRegisteredException
     */
    public function test_forgetLayout_removes_relations(): void
    {
        $layout = Layout::query()->create([
            'name'   => 'L',
            'status' => true,
        ]);
        $product = LayoutTestProduct::factory()->create();
        $product->storeLayout($layout, 'shop', null);

        $product->forgetLayout('shop', null);

        $this->assertNull($product->layoutRelatable()->where('application', 'shop')->first());
        $this->assertDatabaseMissing(config('layout.tables.layout_relation'), [
            'relatable_id' => $product->id,
            'application'  => 'shop',
        ]);
    }

    /**
     * hasLayout returns true when model has layout for given application/collection.
     *
     * @throws ApplicationNotRegisteredException
     * @throws CollectionPropertyNotExistException
     */
    public function test_hasLayout_returns_true_when_layout_resolved(): void
    {
        $layout = Layout::query()->create(['name' => 'L', 'status' => true]);
        $product = LayoutTestProduct::factory()->create();
        $product->storeLayout($layout, 'shop', null);

        $this->assertTrue($product->hasLayout('shop', null));
    }

    /**
     * hasLayout returns false when no layout stored for application/collection.
     *
     * @throws CollectionPropertyNotExistException
     */
    public function test_hasLayout_returns_false_when_no_layout(): void
    {
        $product = LayoutTestProduct::factory()->create();

        $this->assertFalse($product->hasLayout('shop', null));
    }

    /**
     * getLayoutRelation returns LayoutRelation for application/collection without fallback.
     *
     * @throws ApplicationNotRegisteredException
     * @throws CollectionPropertyNotExistException
     */
    public function test_getLayoutRelation_returns_relation_without_fallback(): void
    {
        $layout = Layout::query()->create(['name' => 'L', 'status' => true]);
        $product = LayoutTestProduct::factory()->create();
        $product->storeLayout($layout, 'shop', null);

        $relation = $product->getLayoutRelation('shop', null);

        $this->assertInstanceOf(LayoutRelation::class, $relation);
        $this->assertSame($layout->id, $relation->layout_id);
    }

    /**
     * scopeHasLayout filters to models that have layout for given application.
     *
     * @throws ApplicationNotRegisteredException
     */
    public function test_scopeHasLayout_filters_models_with_relation(): void
    {
        $layout = Layout::query()->create(['name' => 'L', 'status' => true]);
        $p1 = LayoutTestProduct::factory()->create();
        $p2 = LayoutTestProduct::factory()->create();
        $p1->storeLayout($layout, 'shop', null);

        $found = LayoutTestProduct::query()->hasLayout('shop')->get();

        $this->assertCount(1, $found);
        $this->assertSame($p1->id, $found->first()->id);
    }

    /**
     * layoutGetCollectionValue throws CollectionPropertyNotExistException when collection field missing on model.
     */
    public function test_layoutGetCollectionValue_throws_when_field_missing(): void
    {
        $product = new class extends Model
        {
            use HasLayout;

            protected $table = 'layout_test_products';

            protected function layoutCollectionField(): ?string
            {
                return 'non_existent_field';
            }
        };
        $product->id = 1;
        $product->exists = true;

        $this->expectException(CollectionPropertyNotExistException::class);

        $product->layoutGetCollectionValue();
    }

    /**
     * syncLayoutsForApplication replaces existing layout relation with given layout(s) for application.
     *
     * @throws ApplicationNotRegisteredException
     */
    public function test_syncLayoutsForApplication_replaces_relation(): void
    {
        $layout1 = Layout::query()->create([
            'name'   => 'L1',
            'status' => true,
        ]);
        $layout2 = Layout::query()->create([
            'name'   => 'L2',
            'status' => true,
        ]);
        $product = LayoutTestProduct::factory()->create();
        $product->storeLayout($layout1, 'shop', null);

        $product->syncLayoutsForApplication('shop', [$layout2], null);

        $relation = $product->layoutRelatable()->where('application', 'shop')->first();
        $this->assertNotNull($relation);
        $this->assertSame($layout2->id, $relation->layout_id);
        $this->assertDatabaseHas(config('layout.tables.layout_relation'), [
            'relatable_id' => $product->id,
            'application'  => 'shop',
            'layout_id'    => $layout2->id,
        ]);
    }
}
