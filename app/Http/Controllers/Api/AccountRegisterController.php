<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Propaganistas\LaravelPhone\PhoneNumber;
use App\Http\Requests\Api\AccountRegisterStepOneRequest;
use App\Http\Requests\Api\AccountRegisterStepTwoRequest;
use App\Helpers\VerificationCodeHelpers;
use App\Models\User;
use App\Http\Requests\Api\AccountRegisterStepThreeRequest;
use Illuminate\Support\Facades\Hash;

class AccountRegisterController extends Controller
{
    protected function checkRegister(Request $request)
    {
        $allowedCountryCodes = [
            55 => 'BR',
        ];

        $countryCode = $request->input('phone_country') ?: null;

        $countryCode = is_numeric($countryCode) ? $allowedCountryCodes[$countryCode] ?? null : $countryCode;

        if ($countryCode) {
            $request->merge([
                'phone_country' => $countryCode,
            ]);
        }

        $request->validate([
            'phone_country' => 'required|in:' . implode(',', array_values($allowedCountryCodes)),
            'verification_method' => 'required|in:sms,whatsapp,telegram',
            'phone_number' => [
                'required',
                'required',
                'phone:phone_country',
                Rule::unique('users')
                    ->where(
                        fn ($query) => $query->where(
                            'phone_country',
                            $request->input('phone_country')
                        )
                            ->where('phone_number', $request->input('phone_number'))
                    ),
            ],
        ]);

        return $request;
    }

    public function registerStepOne(AccountRegisterStepOneRequest $request)
    {
        $phone = new PhoneNumber($request->input('phone_number'), $request->input('phone_country'));

        $verificationCode = VerificationCodeHelpers::make('registerStepOne');
        $code = $verificationCode?->plainTextValue;

        //TODO: Aqui (emitir evento/dispatch job) para enviar $code via email/sms/WA/TG

        $reponseData = [
            'verify_token' => $verificationCode?->token,
            'message' => __('account/account.registration.verification_code_sended', [
                'verification_method' => 'sms',
            ]),
        ];

        return response()->json($reponseData);
    }

    /**
     * registerStepTwo function
     *
     * Validate code
     * @param AccountRegisterStepTwoRequest $request
     */
    public function registerStepTwo(AccountRegisterStepTwoRequest $request)
    {
        $verifyToken = $request->input('verify_token');
        $code = $request->input('verify_code');

        if (
            !VerificationCodeHelpers::check(
                token: $verifyToken,
                code: $code,
                verifiedOnly: false,
                deleteAfterCheck: false
            )
        ) {
            return response()->json([
                'message' => __('account/account.registration.invalid_token'),
            ], 422);
        }

        return response()->json([
            'message' => __('account/account.registration.token_is_valid'),
            'verified_token' => $verifyToken,
        ]);
    }

    /**
     * registerStepThree function
     * Create user account
     *
     * @param AccountRegisterStepThreeRequest $request
     */
    public function registerStepThree(AccountRegisterStepThreeRequest $request)
    {
        $verifiedToken = $request->input('verified_token');
        $code = $request->input('verify_code');

        if (
            !VerificationCodeHelpers::checkVerified(
                token: $verifiedToken,
                code: $code,
                deleteAfterCheck: true,
            )
        ) {
            return response()->json([
                'message' => __('account/account.registration.invalid_token'),
            ], 422);
        }

        $phoneNumber = preg_replace('(\D+)', '', "{$request->input('phone_number')}");
        $verificationMethod = $request->input('verification_method');
        $now = now();

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make(str()->random(10)),
            'phone_country' => $request->input('phone_country'),
            'phone_number' => $phoneNumber,
            'phone_sms_verified_at' => ($verificationMethod === 'sms') ? $now : null,
            'phone_whatsapp_verified_at' => in_array($verificationMethod, [
                'whatsapp',
                'wa',
            ]) ? $now : null,
            'phone_telegram_verified_at' => in_array($verificationMethod, [
                'telegram',
                'tg',
            ]) ? $now : null,
        ]);

        if (!$user) {
            return response()->json([
                'message' => __('account/account.registration.undefined_fail'),
            ], 422);
        }

        $userToken = $user?->createToken('registration', ['none']);

        return response()->json([
            'message' => __('account/account.registration.success_on_register'),
            'accessToken' => [
                'expires_at' => $userToken?->accessToken?->expires_at ?? null,
                'abilities' => $userToken?->accessToken?->abilities ?? null,
                'plainTextToken' => $userToken?->plainTextToken ?? null,
            ],
        ]);
    }
}
