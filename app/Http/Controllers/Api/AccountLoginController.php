<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AccountLoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'verification_method' => 'required|in:sms,whatsapp,telegram',
            'phone_country' => 'required',
            'phone_number' => 'required',
        ]);

        // TODO
        // Aqui buscar o usuário e enviar uma mensagem via SMS, WA ou Telegram para confirmar conta

        $user = User::getUserByPhone(
            phoneCountryCode: $request->input('phone_country'),
            phoneNumber: $request->input('phone_number'),
            activeOnly: false,
        );

        if (!$user || $user?->trashed()) {
            return response()->json([
                'message' => __('account/account.login.failed'),
                'suggested_action' => 'REGISTER_NEW_ACCOUNT',
                'suggested_action_message' => __('account/account.login.suggested_when_account_not_exist'),
                'success' => false,
            ], 302);
        }

        return response()->json([
            'message' => __('account/account.login.verification_code_sended', [
                'verification_method' => $request->get('verification_method'),
            ]),
        ]);
    }

    public function getVerificationToken(Request $request)
    {
        $request->validate([
            'verification_method' => 'required|in:sms,whatsapp,telegram',
            'phone_country' => 'required',
            'phone_number' => 'required',
        ]);

        // TODO
        // Aqui buscar o usuário e enviar uma mensagem via SMS, WA ou Telegram para confirmar conta

        $user = User::getUserByPhone(
            phoneCountryCode: $request->input('phone_country'),
            phoneNumber: $request->input('phone_number'),
            activeOnly: false,
        );

        if (!$user || $user?->trashed()) {
            return response()->json([
                'message' => __('account/account.login.failed'),
                'suggested_action' => 'REGISTER_NEW_ACCOUNT',
                'suggested_action_message' => __('account/account.login.suggested_when_account_not_exist'),
                'success' => false,
            ], 302);
        }

        return response()->json([
            'message' => __('account/account.login.verification_code_sended', [
                'verification_method' => $request->get('verification_method'),
            ]),
        ]);
    }
}
