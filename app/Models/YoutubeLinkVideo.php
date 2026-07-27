<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class YoutubeLinkVideo extends Model
{
    use SoftDeletes;
    protected $table = 'youtube_links_video';

    protected $fillable = [
        'lesson_video_id',
        'name',
        'order',
        'youtube_link',
        'video_time',
        'is_active',
    ];

    protected $casts = [
        'video_time' => 'integer',
        'is_active' => 'boolean',
    ];

    public function lessonVideo(): BelongsTo
    {
        return $this->belongsTo(LessonVideo::class, 'lesson_video_id');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (YoutubeLinkVideo $youtubeLinkVideo) {
            if (empty($youtubeLinkVideo->order)) {
                $lastOrder = self::where('lesson_video_id', $youtubeLinkVideo->lesson_video_id)->max('order') ?? 0;
                $youtubeLinkVideo->order = $lastOrder + 1;
            }
        });
    }

    public function updateOrder(array $orderedIds): void
    {
        DB::transaction(function () use ($orderedIds) {
            foreach ($orderedIds as $order => $id) {
                self::where('id', $id)->where('lesson_video_id', $this->lesson_video_id)
                    ->update(['order' => $order + 1]);
            }
        });
    }
}
