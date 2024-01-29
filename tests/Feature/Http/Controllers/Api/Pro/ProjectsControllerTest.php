<?php

namespace Tests\Feature\Http\Controllers\Api\Pro;

use App\Models\Professional;
use App\Models\Project;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;
use App\Models\User;
use App\Models\ProfessionalProject;
use App\Enums\ProjectStatus;

class ProjectsControllerTest extends TestCase
{
    /**
     * @test
     */
    public function testIndex(): void
    {
        $user = User::factory()->createOne();
        $response = $this
            ->actingAs($user)
            ->getJson(route('api.public.professional.products.index'));

        $response->assertStatus(200);

        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->whereType('data', 'array')
                ->whereType('links', 'array')
                ->whereType('per_page', 'integer')
                ->whereType('current_page', 'null|integer')
                ->whereType('data.0.extra_info', 'null|array')
                ->whereType('next_page_url', 'string')
                ->etc()
        );
    }

    /**
     * @test
     */
    public function testProductShow(): void
    {
        $project = Project::factory()->createOne([
            'status' => ProjectStatus::OPEN_TO_PROPOSALS?->value,
            'max_of_bids' => 5,
            'total_of_bids' => 2,
        ]);

        $user = User::factory()->createOne();
        $response = $this
            ->actingAs($user)
            ->getJson(route('api.public.professional.products.show', $project?->id));

        // $response->dd();
        $response->assertStatus(200);

        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->whereType('id', 'integer')
                ->whereType('description', 'string')
                ->etc()
        );
    }

    /**
     * @test
     */
    public function testProductReleased(): void
    {
        $project = Project::factory()->createOne([
            'status' => ProjectStatus::OPEN_TO_PROPOSALS?->value,
            'max_of_bids' => 5,
            'total_of_bids' => 2,
        ]);

        $user = User::factory()->createOne();

        $professional = Professional::factory()->createOne([
            'user_id' => $user?->id,
        ]);

        $this->assertTrue(boolval($professional));

        $professionalProject = ProfessionalProject::factory()->createOne([
            'professional_id' => $professional?->id,
            'project_id' => $project?->id,
        ]);

        $this->assertTrue(boolval($professionalProject));

        $response = $this
            ->actingAs($user)
            ->getJson(route('api.public.professional.products.released', $project?->id));

        $response->assertStatus(200);

        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->whereType('project.id', 'integer')
                ->whereType('project.description', 'string')
                ->etc()
        );
    }

    //TODO:
    // - SUCCESS: release project (need projectId, UserId, coinToPay[need be equal in project or -discount])
    // - FAIL: expired project show
    // - FAIL: not released project show
    // - FAIL: exceded total_of_bids project show
}
