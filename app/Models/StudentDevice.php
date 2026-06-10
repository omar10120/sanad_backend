<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentDevice extends Model
{
    protected $fillable = [
        'student_id',
        'device_id',
        'is_current',
        'first_login_at',
        'last_login_at',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'first_login_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    /**
     * Get the student that owns the device relationship.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the device that belongs to the student.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * Update the last login timestamp.
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Set this device as the current device for the student.
     */
    public function setAsCurrent(): void
    {
        // First, set all other devices for this student as not current
        static::where('student_id', $this->student_id)
            ->where('id', '!=', $this->id)
            ->update(['is_current' => false]);

        // Then set this device as current
        $this->update(['is_current' => true]);
    }
}
