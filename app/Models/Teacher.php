<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'estimation_time',
        'whatsapp_link',
        'instagram_link',
        'phone',
        'photo',
        'price',
        'description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function subjectVideos(): BelongsToMany
    {
        return $this->belongsToMany(SubjectVideo::class, 'teacher_has_subject_video', 'teacher_id', 'subject_video_id');
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function canBeDeleted(): bool
    {
        return $this->units()->count() === 0;
    }

    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function (Teacher $teacher) {
            if (!$teacher->canBeDeleted()) {
                throw new Exception(trans('main_trans.Teacher_has_related_data'));
            }
        });
    }
}
