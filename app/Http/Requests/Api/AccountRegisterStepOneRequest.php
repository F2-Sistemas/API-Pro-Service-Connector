<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class AccountRegisterStepOneRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guest();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $allowedCountryCodes = [
            55 => 'BR',
        ];

        return [
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
                            $this->input('phone_country')
                        )
                            ->where('phone_number', $this->input('phone_number'))
                    ),
            ],
        ];
    }
}
