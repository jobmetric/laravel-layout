<?php

namespace JobMetric\Layout\Tests\Stubs\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JobMetric\Layout\HasLayout;
use JobMetric\Layout\Tests\Stubs\Factories\LayoutTestProductFactory;

/**
 * Stub model for HasLayout feature tests.
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $category_id
 *
 * @method static LayoutTestProduct create(array $attributes = [])
 */
class LayoutTestProduct extends Model
{
    use HasFactory;
    use HasLayout;

    protected $table = 'layout_test_products';

    protected $fillable = ['name', 'category_id'];

    protected static function newFactory(): LayoutTestProductFactory
    {
        return LayoutTestProductFactory::new();
    }

    protected function layoutPageType(): string
    {
        return 'product';
    }

    protected function layoutCollectionField(): ?string
    {
        return 'category_id';
    }
}
