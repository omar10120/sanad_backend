<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable ,HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name_ar',
        'name_en',
        'email',
        'phone',
        'photo',
        'status',
        'password',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class);
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'user_has_subject', 'user_id', 'subject_id')
            ->withPivot('unit_id')
            ->withTimestamps();
    }

    public function hasAccessToSubject(int $subjectId, ?int $unitId = null): bool
    {
        if ($this->hasRole('Owner')) {
            return true;
        }

        $query = $this->subjects()->where('subjects.id', $subjectId);

        if ($unitId !== null) {
            $query->wherePivot('unit_id', $unitId);
        }

        return $query->exists();
    }

    /**
     * @deprecated Use hasAccessToSubject() with optional unit scope.
     */
    public function getAllowedSubjectIds(): array
    {
        return $this->subjects()->pluck('subjects.id')->toArray();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'status' => 'boolean',
            'password' => 'hashed',
        ];
    }

}
