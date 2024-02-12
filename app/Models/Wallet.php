<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUniqueIds;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wallet extends Model
{
    use HasFactory;
    use SoftDeletes;
    // use HasUuids;
    use HasUniqueIds;
    use HasUuids;

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
        'uuid',
        'name',
        'short_description',
        'main',
        'user_id',
        'gold_coins',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        //
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'main' => 'boolean',
        'gold_coins' => 'integer',
    ];

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array
     */
    public function uniqueIds()
    {
        return [
            'uuid',
        ];
    }

    /**
     * Generate a new key for the model.
     *
     * @return string
     */
    public function newUniqueId()
    {
        return Str::uuid()->toString();
    }

    /**
     * Get the user that owns the Wallet
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subtractFromWallet(string $type, int $valueToSubtract): bool
    {
        $attribute = match ($type) {
            'gold_coins', 'gold' => 'gold_coins',
            default => null,
        };

        if (!$attribute) {
            return false;
        }

        $currentBalance = $this->{$attribute} ?? 0;

        if (
            !$currentBalance
            || $currentBalance <= 0
            || $valueToSubtract <= 0
            || $currentBalance < $valueToSubtract
        ) {
            return false;
        }

        $result = ($currentBalance >= $valueToSubtract) && $this->decrement($attribute, $valueToSubtract);

        return boolval($result);
    }
}
