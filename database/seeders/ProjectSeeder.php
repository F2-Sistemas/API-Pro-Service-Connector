<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\ProjectCategory;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProjectCategory::factory(4)
            ->create()
            ->each(function (ProjectCategory $projectCategory) {
                Project::factory(30)
                    ->create([
                        'project_category_id' => $projectCategory->id,
                    ])
                    ->each(fn (Project $project) => $project);
            });
    }
}
