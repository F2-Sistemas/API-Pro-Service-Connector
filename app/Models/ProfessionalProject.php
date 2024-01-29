<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessionalProject extends Model
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
        'professional_id',
        'project_id',
        'professional_project_status',
        'personal_note',
        'archived_at',
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
        'archived_at' => 'datetime',
    ];

    /**
     * Get the user that owns the ProfessionalProject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class, 'professional_id', 'id');
    }

    /**
     * Get the user that owns the ProfessionalProject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
}
