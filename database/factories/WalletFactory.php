<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Wallet>
 */
class WalletFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'name' => str('My wallet')->append(' ')->append(fake()->word())->replace(['.', ','], ''),
            'short_description' => fake()->words(4, true),
            'main' => fake()->boolean(90),
        ];
    }
}
