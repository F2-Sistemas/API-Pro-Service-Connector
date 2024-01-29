<?php

namespace Database\Factories;

use App\Models\ProjectCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\ProjectStatus;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => str(\Arr::random([
                'Website development',
                'Mobile APP development',
                'APP development',
                'APP development',
                'Front website',
                'Front website',
                fake()->words(3, true),
                ...explode(' ', str_repeat('Project ', 4)),
                ...explode(' ', str_repeat('Mobile ', 4)),
                ...explode(' ', str_repeat('APP ', 4)),
                ...explode(' ', str_repeat('Website ', 4)),
                ...explode(' ', str_repeat('Laravel ', 4)),
            ]) . ' ' . fake()->words(2, true))->title()->replace(['.'], ''),
            'slug' => fn (array $attr) => str($attr['title'] ?? uniqid())->prepend(' ')->prepend(uniqid())->slug(),
            'description' => fake()->paragraphs(3, true),
            'max_of_bids' => rand(3, 6),
            'total_of_bids' => fn (array $attr) => rand(0, intval($attr['max_of_bids'] ?? 3)),
            'expires_in' => now()->addDays(1),
            'owner_id' => User::factory(),
            'project_category_id' => ProjectCategory::factory(),
            'urgent' => fake()->boolean(30),
            'extra_info' => fn (array $attr) => [
                'Tipo' => explode(' ', strval($attr['title'] ?? ''), 2)[0] ?? '',
                'Algum item' => fake()->words(3, true),
                'Outra coisa?' => fake()->boolean(),
            ],
            'status' => \Arr::random(ProjectStatus::cases()),
            'coin_price' => rand(10, 90),
            'percent_discount_applied' => \Arr::random([
                ...explode(' ', str_repeat(' ', 10)),
                ...range(10, 50),
            ]) ?: null,
            'promoted' => fake()->boolean(20),
            'country_code' => 'BR',
            'city_code' => null,
            'zip_code' => fake()->regexify('0([1-9]){7}'),
        ];
    }
}
