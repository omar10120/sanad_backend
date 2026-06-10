<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Student extends Authenticatable
{
    use Notifiable;
    use HasApiTokens;
    use SoftDeletes;

    protected $guard = 'student';

    protected $fillable = [
        'first_name',
        'last_name',
        'father_name',
        'type_id',
        'academic_year',
        'city',
        'email',
        'phone',
        'photo',
        'school',
        'status',
        'password',
        'max_devices',
        'phone_verified_at',
    ];

    public const Cities = [
        'damascus' => 'دمشق',
        'damascus_suburb' => 'ريف دمشق',
        'homs' => 'حمص',
        'hama' => 'حماة',
        'aleppo' => 'حلب',
        'idlib' => 'إدلب',
        'tartus' => 'طرطوس',
        'latakia' => 'اللاذقية',
        'deir_ezzor' => 'دير الزور',
        'hasaka' => 'الحسكة',
        'raqqa' => 'الرقة',
        'sweida' => 'السويداء',
        'daraa' => 'درعا',
        'quneitra' => 'القنيطرة',
    ];

    public function codes(): HasMany
    {
        return $this->hasMany(Code::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class);
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
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    /**
     * Get the devices that belong to this student.
     */
    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(Device::class, 'student_devices')
            ->withPivot(['is_current', 'first_login_at', 'last_login_at'])
            ->withTimestamps();
    }

    /**
     * Get the current device for this student.
     */
    public function currentDevice(): BelongsToMany
    {
        return $this->devices()->wherePivot('is_current', true);
    }

    /**
     * Get all student device relationships.
     */
    public function studentDevices(): HasMany
    {
        return $this->hasMany(StudentDevice::class);
    }

    /**
     * Associate a device with this student.
     */
    public function attachDevice(Device $device, bool $setAsCurrent = true): StudentDevice
    {
        $studentDevice = $this->studentDevices()
            ->where('device_id', $device->id)
            ->first();

        if (!$studentDevice) {
            $studentDevice = $this->studentDevices()->create([
                'device_id' => $device->id,
                'is_current' => $setAsCurrent,
                'first_login_at' => now(),
                'last_login_at' => now(),
            ]);
        } else {
            $studentDevice->updateLastLogin();
        }

        if ($setAsCurrent) {
            $studentDevice->setAsCurrent();
        }

        return $studentDevice;
    }

    /**
     * Get the current device ID (for backward compatibility).
     */
    public function getCurrentDeviceIdAttribute(): ?string
    {
        $currentDevice = $this->currentDevice()->first();
        return $currentDevice ? $currentDevice->device_id : null;
    }

    /**
     * Check if student can add a new device.
     */
    public function canAddDevice(): bool
    {
        return $this->getActiveDevicesCount() < $this->max_devices;
    }

    /**
     * Get the count of active devices for this student.
     */
    public function getActiveDevicesCount(): int
    {
        return $this->studentDevices()->count();
    }

    /**
     * Add a device to the student with device limit management.
     */
    public function addDevice(Device $device, bool $forceAdd = false): bool
    {
        if (!$forceAdd && !$this->canAddDevice()) {
            return false;
        }

        // If at max capacity, remove oldest device
        if (!$this->canAddDevice()) {
            $oldestDevice = $this->studentDevices()
                ->orderBy('last_login_at', 'asc')
                ->first();
            if ($oldestDevice) {
                $oldestDevice->delete();
            }
        }

        $this->attachDevice($device);
        return true;
    }

    /**
     * Check if the student has verified their phone number.
     */
    public function hasVerifiedPhone(): bool
    {
        return ! is_null($this->phone_verified_at);
    }

    /**
     * Mark the student's phone as verified.
     */
    public function markPhoneAsVerified(): bool
    {
        return $this->forceFill([
            'phone_verified_at' => $this->freshTimestamp(),
        ])->save();
    }
}
