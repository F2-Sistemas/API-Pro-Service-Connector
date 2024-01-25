<?php

namespace Tests\Feature\Http\Controllers\Api;

use Tests\TestCase;

class AccountLoginControllerTest extends TestCase
{
    /**
     * @test
     */
    public function testAccountLoginFail(): void
    {
        $response = $this->postJson(route('api.auth.account.login'));
        $response->assertStatus(422);
        $response->assertInvalid([
            'verification_method' => 'The verification method field is required.',
            'phone_country' => 'The phone country field is required.',
            'phone_number' => 'The phone number field is required.',
        ]);
    }

    /**
     * @test
     */
    public function testAccountLoginInvalidPhone(): void
    {
        /*
        // TODO
        $response = $this->postJson(
            route('api.auth.account.login'),
            [
                'verification_method' => 'sms',
                'phone_country' => 'BR',
                'phone_number' => static::invalidMobileNumber(),
            ]
        );

        $response->assertStatus(422);

        $response->assertInvalid([
            'phone_number' => 'The phone_country code is invalid.',
        ]);
        */
    }

    /**
     * @test
     */
    public function testAccountRegisterAsSuggestedAction(): void
    {
        $response = $this->postJson(
            route('api.auth.account.login'),
            [
                'verification_method' => 'sms',
                'phone_country' => 'BR',
                'phone_number' => static::validMobileNumber(),
            ]
        );

        $response->assertStatus(302);

        $response->assertJson([
            'message' => __('account/account.login.failed'),
            'suggested_action' => 'REGISTER_NEW_ACCOUNT',
            'suggested_action_message' => __('account/account.login.suggested_when_account_not_exist'),
            'success' => false,
        ]);
    }

    /**
     * @test
     */
    public function testAccountLoginWaitingCode(): void
    {
        // TODO
        $this->markTestSkipped('TODO: Put VerificationCode here');
        $response = $this->postJson(
            route('api.auth.account.login'),
            [
                'verification_method' => 'sms',
                'phone_country' => 'BR',
                'phone_number' => static::validMobileNumber(),
            ]
        );

        $response->assertStatus(200);

        $response->assertJson([
            'message' => __('account/account.login.verification_code_sended', [
                'verification_method' => 'sms',
            ]),
        ]);
    }

    /**
     * @test
     */
    public function testGetLoginVerificationToken(): void
    {
        // TODO
        $this->markTestSkipped('TODO: Put VerificationCode here');

        $response = $this->postJson(
            route('api.auth.account.get_login_verification_token'),
            [
                'verification_method' => 'sms',
                'phone_country' => 'BR',
                'phone_number' => static::validMobileNumber(),
            ]
        );

        $response->assertStatus(200);

        $response->assertJson([
            'message' => __('account/account.login.verification_code_sended', [
                'verification_method' => 'sms',
            ]),
        ]);
    }
}
