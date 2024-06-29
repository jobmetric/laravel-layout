<?php

namespace JobMetric\Layout\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JobMetric\Layout\Models\Layout;

/**
 * @extends Factory<Layout>
 */
class LayoutFactory extends Factory
{
    protected $model = Layout::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'status' => $this->faker->boolean()
        ];
    }

    /**
     * set name
     *
     * @param string $name
     *
     * @return static
     */
    public function setName(string $name): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => $name
        ]);
    }

    /**
     * set status
     *
     * @param bool $status
     *
     * @return static
     */
    public function setStatus(bool $status): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => $status
        ]);
    }
}
