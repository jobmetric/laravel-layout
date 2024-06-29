<?php

namespace JobMetric\Layout\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JobMetric\Layout\Models\LayoutPage;

/**
 * @extends Factory<LayoutPage>
 */
class LayoutPageFactory extends Factory
{
    protected $model = LayoutPage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'layout_id' => null,
            'application' => $this->faker->shuffleArray(['admin', 'web', 'api']),
            'page' => $this->faker->shuffleArray(['home', 'about', 'contact', 'login', 'register'])
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
        return $this->state(fn(array $attributes) => [
            'layout_id' => $layout_id
        ]);
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
            'application' => $application
        ]);
    }

    /**
     * set page
     *
     * @param string $page
     *
     * @return static
     */
    public function setPage(string $page): static
    {
        return $this->state(fn(array $attributes) => [
            'page' => $page
        ]);
    }
}
