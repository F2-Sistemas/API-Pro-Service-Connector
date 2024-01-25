<?php

namespace Tests\Feature\Http\Controllers\Api;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Testing\Fluent\AssertableJson;
use App\Helpers\VerificationCodeHelpers;

class AccountRegisterControllerTest extends TestCase
{
    /**
     * @test
     * Explain: Invalid or missing data
     */
    public function testAccountRegisterFirstStepFailMissingData(): void
    {
        $response = $this->postJson(route('api.auth.account.register.step_one'));

        $response->assertStatus(422);
        $response->assertInvalid([
            'verification_method' => 'The verification method field is required.',
            'phone_country' => 'The phone country field is required.',
            'phone_number' => 'The phone number field is required.',
        ]);
    }

    /**
     * @test
     * Explain: Registered phone number
     */
    public function testAccountRegisterFirstStepFailRegistered(): void
    {
        $userData = [
            'phone_country' => 'BR',
            'phone_number' => static::validMobileNumber(),
            'phone_sms_verified_at' => now(),
        ];

        User::factory($userData)->createOne();

        $response = $this->postJson(route('api.auth.account.register.step_one'), array_merge([
            'verification_method' => 'sms',
        ], Arr::except($userData, ['phone_sms_verified_at'])));

        $response->assertStatus(422);
        $response->assertInvalid([
            'phone_number' => 'The phone number has already been taken.',
        ]);
    }

    /**
     * @test
     */
    public function testAccountRegisterFirstStepFailInvalidCountryCode(): void
    {
        $response = $this->postJson(
            route('api.auth.account.register.step_one'),
            [
                'verification_method' => 'sms',
                'phone_country' => 89798,
                'phone_number' => static::validMobileNumber(),
            ]
        );

        $response->assertStatus(422);

        $response->assertInvalid([
            'phone_country' => 'The selected phone country is invalid.',
            'phone_number' => __('validation.phone'),
        ]);
    }

    /**
     * @test
     */
    public function testAccountRegisterFirstStepFailInvalidPhone(): void
    {
        $response = $this->postJson(
            route('api.auth.account.register.step_one'),
            [
                'verification_method' => 'sms',
                'phone_country' => 'BR',
                'phone_number' => static::inValidMobileNumber(),
            ]
        );

        $response->assertStatus(422);

        $response->assertInvalid([
            'phone_number' => __('validation.phone'),
        ]);
    }

    /**
     * @test
     */
    public function testAccountRegisterFirstStepRequestCode(): void
    {
        $response = $this->postJson(
            route('api.auth.account.register.step_one'),
            [
                'verification_method' => 'sms',
                'phone_country' => 'BR',
                'phone_number' => static::validMobileNumber(),
            ]
        );

        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) =>
            $json
                ->whereType('verify_token', 'string|numeric|integer')
                ->whereType('message', 'string')
        );

        $response->assertJson([
            'message' => __('account/account.registration.verification_code_sended', [
                'verification_method' => 'sms',
            ]),
        ]);
    }

    /**
     * @test
     */
    public function testGetRegisterFailVerificationToken(): void
    {
        $verificationCode = VerificationCodeHelpers::make('registerStepOne');
        $verifyToken = $verificationCode?->token;
        $code = $verificationCode?->plainTextValue;

        $payload = [
            'phone_country' => 'BR',
            'phone_number' => static::validMobileNumber(),
            'verification_method' => 'sms',
            'verify_token' => $verifyToken,
            'verify_code' => $code . '789',
        ];

        $response = $this->postJson(
            route('api.auth.account.register.step_two'),
            $payload
        );

        $response->assertStatus(422);

        $response->assertJson([
            'message' => __('account/account.registration.invalid_token'),
        ]);
    }

    /**
     * @test
     */
    public function testGetRegisterSuccessVerificationToken(): void
    {
        $verificationCode = VerificationCodeHelpers::make('registerStepOne');
        $verifyToken = $verificationCode?->token;
        $code = $verificationCode?->plainTextValue;

        $this->assertTrue(
            VerificationCodeHelpers::check(
                token: $verifyToken,
                code: $code,
                verifiedOnly: false,
                deleteAfterCheck: false,
            )
        );

        $this->assertTrue(
            VerificationCodeHelpers::checkVerified(
                token: $verifyToken,
                code: $code,
                deleteAfterCheck: false,
            )
        );

        $payload = [
            'phone_country' => 'BR',
            'phone_number' => static::validMobileNumber(),
            'verification_method' => 'sms',
            'verified_token' => $verifyToken,
            'verify_code' => $code,
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
        ];

        $response = $this->postJson(
            route('api.auth.account.register.step_three'),
            $payload
        );

        $response->assertStatus(200);

        $response->assertJson(
            fn (AssertableJson $json) =>
            $json
            ->whereType('accessToken', 'array|null')
                ->whereType('accessToken.expires_at', 'null|string')
                ->whereType('accessToken.abilities', 'null|array')
                ->whereType('message', 'string')
        );

        $response->assertJson([
            'message' => __('account/account.registration.success_on_register'),
        ]);
    }
}
