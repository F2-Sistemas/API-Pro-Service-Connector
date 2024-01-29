<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\ProjectStatus;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;

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
        'title',
        'slug',
        'description',
        'status',
        'max_of_bids',
        'total_of_bids',
        'urgent',
        'expires_in',
        'project_category_id',
        'owner_id',
        'extra_info',
        'coin_price',
        'percent_discount_applied',
        'promoted',
        'country_code',
        'city_code',
        'zip_code',
        'prioritize_qualified_professionals',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'owner_id',
        'prioritize_qualified_professionals',
    ];

    protected $appends = [
        'priceWithDiscount',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_in' => 'datetime',
        'urgent' => 'boolean',
        'extra_info' => AsCollection::class,
        'status' => ProjectStatus::class,
        'coin_price' => 'integer',
        'percent_discount_applied' => 'integer',
        'promoted' => 'boolean',
        'prioritize_qualified_professionals' => 'boolean',
        'priceWithDiscount' => 'integer',
    ];

    public function scopeActiveOnly(Builder $query)
    {
        return $query
            ->where('status', ProjectStatus::OPEN_TO_PROPOSALS?->value)
            ->whereRaw('max_of_bids > total_of_bids')
            ->where(
                fn (Builder $q) => $q->whereNull('expires_in')
                    ->orWhere('expires_in', '>=', now()->addMinutes(5))
            );
    }

    /**
     * Get the owner that owns the Project
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }

    /**
     * Get the project category that owns the Project
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function projectCategory(): BelongsTo
    {
        return $this->category();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id', 'id');
    }

    /**
     * Get all of the professionalProject for the Project
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function professionalProject(): HasMany
    {
        return $this->hasMany(ProfessionalProject::class, 'project_id', 'id');
    }

    public function getPriceWithDiscountAttribute()
    {
        $discount = $this->percent_discount_applied;
        $discount = $discount > 0 && $discount <= 100 ? $discount : null;

        if (!$discount) {
            return intval($this->coin_price);
        }

        return intval($this->coin_price - ($this->coin_price / 100 * $discount));
    }

    public function enterCoinIsValid(int $coin)
    {
        return in_array($coin, [
            $this->getPriceWithDiscountAttribute(),
            $this->coin_price,
        ]);
    }
}
