<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Professional;
use App\Models\Project;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProfessionalProject>
 */
class ProfessionalProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'professional_id' => Professional::factory(),
            'project_id' => Project::factory(),
            'professional_project_status' => null,
            'personal_note' => 'Factory note',
            'archived_at' => fake()->boolean(1) ? now() : null,
        ];
    }
}
