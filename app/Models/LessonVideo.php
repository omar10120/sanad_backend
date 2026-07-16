<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class LessonVideo extends Model
{
    use SoftDeletes;

    protected $table = 'lessons_video';

    protected $fillable = [
        'title',
        'unit_id',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function youtubeLinks(): HasMany
    {
        return $this->hasMany(YoutubeLinkVideo::class, 'lesson_video_id')->orderBy('order');
    }

    public function canBeDeleted(): bool
    {
        return $this->youtubeLinks()->count() === 0;
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (LessonVideo $lessonVideo) {
            if (empty($lessonVideo->order)) {
                $lastOrder = self::where('unit_id', $lessonVideo->unit_id)->max('order') ?? 0;
                $lessonVideo->order = $lastOrder + 1;
            }
        });

        static::deleting(function (LessonVideo $lessonVideo) {
            if (!$lessonVideo->canBeDeleted()) {
                throw new Exception(trans('main_trans.Lesson_video_has_related_data'));
            }
        });
    }

    public function updateOrder(array $orderedIds): void
    {
        DB::transaction(function () use ($orderedIds) {
            foreach ($orderedIds as $order => $id) {
                self::where('id', $id)->where('unit_id', $this->unit_id)
                    ->update(['order' => $order + 1]);
            }
        });
    }
}
