<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;
use Propaganistas\LaravelPhone\Casts\RawPhoneNumberCast;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property mixed $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $stripe_id
 * @property string|null $pm_type
 * @property string|null $pm_last_four
 * @property string|null $trial_ends_at
 * @property string|null $phone_country
 * @property string|null $phone_number
 * @property string|null $phone_sms_verified_at
 * @property string|null $phone_whatsapp_verified_at
 * @property string|null $phone_telegram_verified_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhoneCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhoneSmsVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhoneTelegramVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhoneWhatsappVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePmLastFour($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePmType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStripeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTrialEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_country',
        'phone_number',
        'phone_sms_verified_at',
        'phone_whatsapp_verified_at',
        'phone_telegram_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'phone_number' => RawPhoneNumberCast::class . ':phone_country',
    ];

    public function scopeByPhone(
        Builder $query,
        null|int|string $phoneCountryCode,
        null|int|string $phoneNumber,
        ?bool $activeOnly = null,
    ): Builder {
        $phoneCountryCode = preg_replace('/[^0-9]/', '', "{$phoneCountryCode}");
        $phoneNumber = preg_replace('/[^0-9]/', '', "{$phoneNumber}");

        $query = $query
            ->whereNotNull('phone_country')
            ->whereNotNull('phone_number')
            ->where('phone_country', $phoneCountryCode)
            ->where('phone_number', $phoneNumber);

        if (is_null($activeOnly)) {
            return $query;
        }

        if ($activeOnly) {
            return $query->withoutTrashed();
        }

        return $query->withTrashed();
    }

    public static function getUserExists(
        null|int|string $phoneCountryCode,
        null|int|string $phoneNumber,
        ?bool $activeOnly = null,
    ): bool {
        return static::byPhone(
            $phoneCountryCode,
            $phoneNumber,
            $activeOnly,
        )
            ->exists();
    }

    public static function getUserByPhone(
        null|int|string $phoneCountryCode,
        null|int|string $phoneNumber,
        ?bool $activeOnly = null,
    ): ?static {
        return static::byPhone(
            $phoneCountryCode,
            $phoneNumber,
            $activeOnly,
        )
            ->first();
    }

    public static function getByPhone(
        null|int|string $phoneCountryCode,
        null|int|string $phoneNumber,
        ?bool $activeOnly = null,
    ): ?static {
        return static::getUserByPhone(
            $phoneCountryCode,
            $phoneNumber,
            $activeOnly,
        );
    }

    /**
     * Get the professional associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function professional(): HasOne
    {
        return $this->hasOne(Professional::class);
    }
}
