<?php

namespace App\Models;

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
        'telegram_link',
        'phone',
        'photo',
        'price',
        'description',
        'is_active',
    ];

    protected $casts = [
        'price' => 'string',
        'is_active' => 'boolean',
    ];

    public function subjectVideos(): BelongsToMany
    {
        return $this->belongsToMany(SubjectVideo::class, 'teacher_has_subject_video', 'teacher_id', 'subject_video_id')
            ->withPivot('order');
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }
}
