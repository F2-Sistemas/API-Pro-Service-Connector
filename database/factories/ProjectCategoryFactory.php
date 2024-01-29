<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectCategory>
 */
class ProjectCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->words(3, true),
            'slug' => fn (array $attr) => str($attr['title'] ?? uniqid())->prepend(' ')->prepend(uniqid())->slug(),
            'coin_weight' => rand(1, 5),
            'icon' => null,
        ];
    }
}
