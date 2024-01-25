<?php

namespace App\Http\Requests\Api;

use App\Models\VerificationCode;

class AccountRegisterStepThreeRequest extends AccountRegisterStepOneRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $parentRules = parent::rules();
        unset($parentRules['verify_token']);
        $parentRules['verified_token'] =  'required|exists:' . VerificationCode::class . ',token';
        $parentRules['verify_code'] =  'required';
        $parentRules['name'] = 'required|string|min:3';
        $parentRules['email'] = 'required|email';

        return $parentRules;
    }
}
