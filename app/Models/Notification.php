<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'type',
        'target_type',
        'target_ids',
        'created_by',
        'status',
        'scheduled_at',
        'sent_at',
        'total_recipients',
        'successful_sends',
        'failed_sends',
        'is_active',
    ];

    protected $casts = [
        'target_ids' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    public function getTargetStudentsQuery()
    {
        $query = Student::where('status', 1);

        return match ($this->target_type) {
            'type' => $query->whereIn('type_id', $this->target_ids ?? []),
            'student' => $query->whereIn('id', $this->target_ids ?? []),
            default => $query,
        };
    }

    public function canBeSent(): bool
    {
        return in_array($this->status, ['draft', 'failed', 'processing'], true) && $this->is_active;
    }

    public function getSuccessRateAttribute(): float
    {
        if ($this->total_recipients === 0) {
            return 0;
        }

        return ($this->successful_sends / $this->total_recipients) * 100;
    }
}
