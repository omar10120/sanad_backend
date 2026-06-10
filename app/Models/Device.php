<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    protected $fillable = [
        'device_id',
        'brand',
        'device',
        'manufacturer',
        'model',
        'product',
        'name',
        'os_version',
        'os_name',
        'fcm_token',
        'is_active',
        'last_active_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_active_at' => 'datetime',
    ];

    /**
     * Get the students that belong to this device.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_devices')
            ->withPivot(['is_current', 'first_login_at', 'last_login_at'])
            ->withTimestamps();
    }

    /**
     * Get the current student for this device.
     */
    public function currentStudent(): BelongsToMany
    {
        return $this->students()->wherePivot('is_current', true);
    }

    /**
     * Get all student device relationships.
     */
    public function studentDevices(): HasMany
    {
        return $this->hasMany(StudentDevice::class);
    }

    /**
     * Scope to get only active devices.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get devices by device_id.
     */
    public function scopeByDeviceId($query, $deviceId)
    {
        return $query->where('device_id', $deviceId);
    }

    /**
     * Update the last active timestamp.
     */
    public function updateLastActive(): void
    {
        $this->update(['last_active_at' => now()]);
    }
}
