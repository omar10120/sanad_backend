<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubjectVideo extends Model
{
    use SoftDeletes;

    protected $table = 'subjects_video';

    protected $fillable = [
        'name',
        'icon',
        'link',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function types(): BelongsToMany
    {
        return $this->belongsToMany(Type::class, 'type_has_subject_video', 'subject_video_id', 'type_id');
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'teacher_has_subject_video', 'subject_video_id', 'teacher_id');
    }

    public function canBeDeleted(): bool
    {
        return $this->teachers()->count() === 0;
    }

    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function (SubjectVideo $subjectVideo) {
            if (!$subjectVideo->canBeDeleted()) {
                throw new Exception(trans('main_trans.Subject_video_has_related_data'));
            }
        });
    }
}
