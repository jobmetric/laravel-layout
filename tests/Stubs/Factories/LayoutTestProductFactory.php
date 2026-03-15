<?php

namespace JobMetric\Layout\Tests\Stubs\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JobMetric\Layout\Tests\Stubs\Models\LayoutTestProduct;

/**
 * @extends Factory<LayoutTestProduct>
 */
class LayoutTestProductFactory extends Factory
{
    protected $model = LayoutTestProduct::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'        => $this->faker->word,
            'category_id' => null,
        ];
    }

    public function setCategoryId(?string $categoryId): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => $categoryId,
        ]);
    }
}
