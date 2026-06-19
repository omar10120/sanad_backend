<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitWithContentResource extends JsonResource
{
    protected bool $isLocked;

    public function __construct($resource, bool $isLocked = false)
    {
        parent::__construct($resource);
        $this->isLocked = $isLocked;
    }

    public function toArray(Request $request): array
    {
        $lessonVideos = $this->relationLoaded('lessonVideos')
            ? $this->lessonVideos
            : collect();

        if ($this->isLocked) {
            $firstLesson = $lessonVideos->first();
            $lessonVideos = $firstLesson ? collect([$firstLesson]) : collect();
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_order' => $this->order,
            'teacher_id' => $this->teacher_id,
            'number_of_lessons' => $this->lesson_videos_count ?? $lessonVideos->count(),
            'is_locked' => $this->isLocked,
            'lesson_videos' => LessonVideoResource::collection(
                $lessonVideos->map(fn ($lessonVideo) => new LessonVideoResource($lessonVideo, $this->isLocked))
            ),
        ];
    }
}
