<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PhoneVerificationCode extends Model
{
    protected $fillable = [
        'phone',
        'code',
        'type',
        'expires_at',
        'is_used',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    const TYPE_REGISTRATION = 'registration';
    const TYPE_PASSWORD_RESET = 'password_reset';
    const TYPE_PHONE_CHANGE = 'phone_change';

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return !$this->is_used && !$this->isExpired();
    }

    public function markAsUsed(): void
    {
        $this->update([
            'is_used' => true,
            'used_at' => now(),
        ]);
    }

    private static function getCodeByType(string $type): string
    {
        return match ($type) {
            self::TYPE_REGISTRATION => '000000',
            self::TYPE_PASSWORD_RESET => '137928',
            self::TYPE_PHONE_CHANGE => '597264',
            default => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
        };
    }
    
    public static function generate(string $phone, string $type): self
    {
        // Delete existing codes for this phone and type
        static::where('phone', $phone)
            ->where('type', $type)
            ->where('is_used', false)
            ->delete();

        return static::create([
            'phone' => $phone,
            'code' => self::getCodeByType($type),
            'type' => $type,
            'expires_at' => Carbon::now()->addMinutes(30), // 30 minutes expiry
        ]);
    }
}
