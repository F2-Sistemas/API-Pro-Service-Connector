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
        $project = Project::activeOnly()->first() ?: Project::factory()->createOne([
            'status' => ProjectStatus::OPEN_TO_PROPOSALS?->value,
            'max_of_bids' => 5,
            'total_of_bids' => 2,
        ]);

        $user = User::factory()->createOne();

        if (!Project::query()->activeOnly()->count()) {
            Project::factory(4)->create([
                'status' => ProjectStatus::OPEN_TO_PROPOSALS,
                'max_of_bids' => 5,
                'total_of_bids' => 1,
                'expires_in' => now()->addDays(7),
            ]);
        }

        $professional = Professional::factory()->createOne([
            'user_id' => $user?->id,
        ]);

        $this->assertTrue(boolval($professional));

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
                ->whereType('next_page_url', 'null|string')
                ->etc()
        );
    }

    /**
     * @test
     */
    public function testProjectShow(): void
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

        $response->assertStatus(200);

        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->whereType('id', 'integer')
                ->whereType('description', 'string')
                ->etc()
        );
    }

    public function testProjectCategoryList(): void
    {
        $user = User::factory()->createOne();

        $response = $this
            ->actingAs($user)
            ->getJson(route('api.public.professional.category.index'));

        $response->assertStatus(200);

        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->whereType('data', 'array')
                ->whereType('links', 'array')
                ->whereType('per_page', 'integer')
                ->whereType('current_page', 'null|integer')
                ->whereType('data.0.id', 'integer')
                ->whereType('data.0.title', 'string')
                ->whereType('data.0.slug', 'string')
                ->whereType('next_page_url', 'null|string')
                ->etc()
        );
    }

    /**
     * @test
     */
    public function testProjectRelease(): void
    {
        $coinPrice = rand(10, 90);

        $project = Project::factory()->createOne([
            'status' => ProjectStatus::OPEN_TO_PROPOSALS?->value,
            'max_of_bids' => 5,
            'total_of_bids' => 2,
            'coin_price' => $coinPrice,
        ]);

        $user = User::factory()->createOne();

        // TODO: aqui, por garantia, adicionar saldo à carteira do usuário (ou testar com e sem saldo)

        $professional = Professional::factory()->createOne([
            'user_id' => $user?->id,
        ]);

        $this->assertTrue(boolval($professional));

        $coinValue = $coinPrice;

        $response = $this
            ->actingAs($user)
            ->getJson(route('api.public.professional.products.release', [$project?->id, $coinValue]));

        $response->assertStatus(200);

        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->whereType('project.id', 'integer')
                ->whereType('project.description', 'string')
                ->etc()
        );

        $professionalProjectExists = ProfessionalProject::query()
            ->where('professional_id', $professional?->id)
            ->where('project_id', $project?->id)->exists();

        $this->assertTrue(boolval($professionalProjectExists));
    }

    /**
     * @test
     */
    public function testProjectReleased(): void
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

    /**
     * @test
     * @dataProvider expiredProjectprovider
     */
    public function testProjectReleaseExpiration(
        array $data,
        int $assertStatus,
        array|null $assertJson = null,
        int|null $coinPrice = null,
        int|null $assertCoinPrice = null,
    ): void {
        $coinPrice ??= rand(10, 90);

        $project = Project::factory()->createOne(array_merge(
            [
                'status' => ProjectStatus::OPEN_TO_PROPOSALS?->value,
                'coin_price' => $coinPrice,
            ],
            $data,
        ));

        $user = User::factory()->createOne();

        $professional = Professional::factory()->createOne([
            'user_id' => $user?->id,
        ]);

        $this->assertTrue(boolval($professional));

        $coinValue = $assertCoinPrice ?? $coinPrice;

        $response = $this
            ->actingAs($user)
            ->getJson(route('api.public.professional.products.release', [$project?->id, $coinValue]));

        $response->assertStatus($assertStatus);

        if (!$assertJson) {
            return;
        }

        $assertJson = array_map(function ($item) {
            if (!is_callable($item)) {
                return $item;
            }

            return call_user_func($item);
        }, $assertJson);

        $response->assertJson($assertJson);
    }

    public static function expiredProjectprovider(): array
    {
        return [
            [
                'data' => [
                    'max_of_bids' => 5,
                    'total_of_bids' => 2,
                    'expires_in' => now()->subMinutes(5),
                ],
                'assertStatus' => 404,
                'assertJson' => ['error' => fn () => __('Not found!')],
                'coinPrice' => null,
            ],
            [
                'data' => [
                    'max_of_bids' => 5,
                    'total_of_bids' => 5,
                    'expires_in' => now()->addDays(1),
                ],
                'assertStatus' => 404,
                'assertJson' => ['error' => fn () => __('Not found!')],
                'coinPrice' => null,
            ],
            [
                'data' => [
                    'max_of_bids' => 5,
                    'total_of_bids' => 2,
                    'expires_in' => now()->addDays(1),
                ],
                'assertStatus' => 200,
                'assertJson' => null,
                'coinPrice' => null,
            ],
            [
                'data' => [
                    'max_of_bids' => 5,
                    'total_of_bids' => 2,
                    'expires_in' => now()->addDays(1),
                ],
                'assertStatus' => 422,
                'assertJson' => [
                    'message' => fn () => __('Invalid coin price confirmation.'),
                ],
                'coinPrice' => 104,
                'assertCoinPrice' => 110,
            ],
        ];
    }

    //TODO:
    // - SUCCESS: release project (need projectId, UserId, coinToPay[need be equal in project or -discount])
    // - FAIL: expired project show
    // - Debit coinValue from the professional's balance when release project info
}
