<?php

namespace App\Http\Requests\Api;

use App\Models\VerificationCode;

class AccountRegisterStepTwoRequest extends AccountRegisterStepOneRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $parentRules = parent::rules();
        $parentRules['verify_token'] =  'required|exists:' . VerificationCode::class . ',token';
        $parentRules['verify_code'] =  'required';

        return $parentRules;
    }
}
