<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

#[\AllowDynamicProperties]
class VerificationCode extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    // protected $table = '';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'value',
        'token',
        'expires_in',
        'checked_in',
        'provider',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'value',
        'plainTextValue',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'value' => 'hashed',
        'expires_in' => 'datetime',
        'checked_in' => 'datetime',
    ];

    public function scopeExpiredOnly(Builder $query): Builder
    {
        return $query->where('expires_in', '<=', now());
    }

    public function scopeInvalidOnly(Builder $query): Builder
    {
        return $query
            ->where(
                fn ($q) => $q
                    ->whereNotNull('checked_in')
                    ->orWhere('checked_in', '<', now()->subMinutes(5))
            )
            ->orWhereNull('value')
            ->orWhere('expires_in', '<=', now());
    }

    public function scopeValidOnly(Builder $query): Builder
    {
        return $query
            ->where(
                fn ($q) => $q
                    ->whereNull('checked_in')
                    ->orWhere('checked_in', '>=', now()->subMinutes(5))
            )
            ->WhereNotNull('value')
            ->where(
                fn ($q) => $q
                    ->whereNull('expires_in')
                    ->orWhere('expires_in', '>', now())
            );
    }

    public function scopeValidByToken(Builder $query, string $token, bool $verifiedOnly = false): Builder
    {
        $query = $verifiedOnly ? $this->scopeVerifiedOnly($query) : $this->scopeValidOnly($query);

        return $query->where('token', $token);
    }

    public function scopeVerifiedOnly(Builder $query): Builder
    {
        return $this->scopeValidOnly($query)
            ->whereNotNull('checked_in');
    }

    public function getPlainTextValue(): string|null
    {
        $plainTextValue = $this->plainTextValue ?? null;

        return is_string($plainTextValue) || is_numeric($plainTextValue) ? strval($plainTextValue) : null;
    }
}
