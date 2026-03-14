<?php

namespace JobMetric\Layout\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JobMetric\Layout\Models\LayoutPlugin;

/**
 * @extends Factory<LayoutPlugin>
 */
class LayoutPluginFactory extends Factory
{
    protected $model = LayoutPlugin::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'layout_id' => null,
            'plugin_id' => null,
            'position'  => $this->faker->randomElement([
                'header',
                'content',
                'aside',
                'footer',
            ]),
            'ordering'  => $this->faker->numberBetween(0, 100),
        ];
    }

    /**
     * set layout_id
     *
     * @param int $layout_id
     *
     * @return static
     */
    public function setLayoutId(int $layout_id): static
    {
        return $this->state(fn (array $attributes) => [
            'layout_id' => $layout_id,
        ]);
    }

    /**
     * set plugin_id
     *
     * @param int $plugin_id
     *
     * @return static
     */
    public function setPluginId(int $plugin_id): static
    {
        return $this->state(fn (array $attributes) => [
            'plugin_id' => $plugin_id,
        ]);
    }

    /**
     * set position
     *
     * @param string $position
     *
     * @return static
     */
    public function setPosition(string $position): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => $position,
        ]);
    }

    /**
     * set ordering
     *
     * @param int $ordering
     *
     * @return static
     */
    public function setOrdering(int $ordering): static
    {
        return $this->state(fn (array $attributes) => [
            'ordering' => $ordering,
        ]);
    }
}
