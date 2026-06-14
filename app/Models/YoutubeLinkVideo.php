<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class YoutubeLinkVideo extends Model
{
    protected $table = 'youtube_links_video';

    protected $fillable = [
        'lesson_video_id',
        'name',
        'order',
        'youtube_link',
        'video_time',
    ];

    public function lessonVideo(): BelongsTo
    {
        return $this->belongsTo(LessonVideo::class, 'lesson_video_id');
    }
}
