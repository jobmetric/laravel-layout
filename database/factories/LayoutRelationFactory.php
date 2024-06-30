<?php

namespace JobMetric\Layout\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JobMetric\Layout\Models\LayoutRelation;

/**
 * @extends Factory<LayoutRelation>
 */
class LayoutRelationFactory extends Factory
{
    protected $model = LayoutRelation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'application' => $this->faker->shuffleArray(['admin', 'web', 'api']),
            'relatable_type' => null,
            'relatable_id' => null,
            'category_id' => null,
            'collection' => null,
        ];
    }

    /**
     * set application
     *
     * @param string $application
     *
     * @return static
     */
    public function setApplication(string $application): static
    {
        return $this->state(fn(array $attributes) => [
            'application' => $application,
        ]);
    }

    /**
     * set the relatable
     *
     * @param string $relatable_type
     * @param int $relatable_id
     *
     * @return static
     */
    public function setRelatable(string $relatable_type, int $relatable_id): static
    {
        return $this->state(fn(array $attributes) => [
            'relatable_type' => $relatable_type,
            'relatable_id' => $relatable_id,
        ]);
    }

    /**
     * set layout id
     *
     * @param int $layout_id
     *
     * @return static
     */
    public function setLayoutId(int $layout_id): static
    {
        return $this->state(fn(array $attributes) => [
            'layout_id' => $layout_id,
        ]);
    }

    /**
     * set collection
     *
     * @param string|null $collection
     *
     * @return static
     */
    public function setCollection(?string $collection): static
    {
        return $this->state(fn(array $attributes) => [
            'collection' => $collection,
        ]);
    }
}
