<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    protected $fillable = [
        'teacher_id',
        'name',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function lessonVideos(): HasMany
    {
        return $this->hasMany(LessonVideo::class, 'unit_id');
    }

    public function canBeDeleted(): bool
    {
        return $this->lessonVideos()->count() === 0;
    }

    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function (Unit $unit) {
            if (!$unit->canBeDeleted()) {
                throw new Exception(trans('main_trans.Unit_has_related_data'));
            }
        });
    }
}
