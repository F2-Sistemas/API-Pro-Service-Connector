<?php

namespace Tests\Feature\Http\Controllers\Api\Pro;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use App\Models\Wallet;

class WalletsControllerTest extends TestCase
{
    /**
     * @test
     */
    public function testIndex(): void
    {
        $user = User::factory()->createOne();

        if (!$user->wallets()->count()) {
            $user->wallets()->create(Wallet::factory()->make([
                'user_id' => $user->id,
            ])->toArray());
        }

        $response = $this
            ->actingAs($user)
            ->getJson(route('api.public.professional.wallets.index'));

        $response->assertStatus(200);

        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->whereType('data', 'array')
                ->whereType('links', 'array')
                ->whereType('per_page', 'integer')
                ->whereType('current_page', 'null|integer')
                ->whereType('next_page_url', 'null|string')
                ->whereType('data.0.uuid', 'string|uuid')
                ->whereType('data.0.name', 'string')
                ->whereType('data.0.short_description', 'null|string')
                ->whereType('data.0.main', 'null|boolean')
                ->whereType('data.0.user_id', 'integer')
                ->etc()
        );
    }
}
