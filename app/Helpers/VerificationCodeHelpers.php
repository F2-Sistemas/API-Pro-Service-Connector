<?php

namespace App\Helpers;

use App\Models\VerificationCode;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class VerificationCodeHelpers
{
    public static function check(
        string $token,
        int|string $code,
        bool $verifiedOnly = false,
        bool $deleteAfterCheck = false,
    ): bool {
        $verificationCode =  VerificationCode::validByToken($token, $verifiedOnly)->first();

        if (!$verificationCode) {
            return false;
        }

        $result = Hash::check($code, $verificationCode->value);

        if (!$result) {
            return false;
        }

        if ($deleteAfterCheck) {
            $verificationCode->delete();

            return $result;
        }

        $verificationCode->update([
            'checked_in' => now(),
        ]);

        return $result;
    }

    public static function checkVerified(
        string $token,
        int|string $code,
        bool $deleteAfterCheck = false,
    ): bool {
        return static::check(
            token: $token,
            code: $code,
            verifiedOnly: true,
            deleteAfterCheck: $deleteAfterCheck,
        );
    }

    public static function make(
        ?string $name = null,
        ?string $value = null,
        ?string $token = null,
        \DateTimeInterface|string|null $expiresIn = null,
        ?string $provider = null,
    ): VerificationCode {
        $expiresIn = $expiresIn ? now()->parse($expiresIn) : now()->addDays(1);
        $value = $value ?: rand(111111, 999999);

        $verificationCode = VerificationCode::create([
            'name' => $name,
            'value' => $value ?: Hash::make(rand(100)),
            'token' => $token ?: static::generateHashToken("{$value}"),
            'expires_in' => $expiresIn,
            'checked_in' => null,
            'provider' => $provider,
        ]);

        $verificationCode->plainTextValue = $value;

        return $verificationCode;
    }

    public static function generateHashToken(
        ?string $data = null,
        ?string $algo = 'SHA256',
        ?string $provider = null,
    ): string {
        $data = $data ?: Str::random(8);
        $hash = base64_encode(
            json_encode(array_filter([
                'alg' => $algo,
                'provider' => $provider,
                'data_len' => strlen($data),
                'time' => time(),
            ]), 64 | 128)
        );

        return hash(
            $algo,
            sprintf(
                '%s%s%s',
                str_replace(['='], '', trim($hash)),
                rand(1000, 1500),
                $data,
            )
        );
    }
}
