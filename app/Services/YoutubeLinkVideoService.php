<?php

namespace App\Services;

use App\Models\LessonVideo;
use App\Models\SubjectVideo;
use App\Models\Teacher;
use App\Models\Unit;
use App\Models\YoutubeLinkVideo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use App\Facades\Time;   



class YoutubeLinkVideoService
{
    public function getYoutubeLinksByLessonVideo(int $lessonVideoId): Collection
    {
        return YoutubeLinkVideo::where('lesson_video_id', $lessonVideoId)
            ->orderBy('order')
            ->get();
    }

    public function getArchivedYoutubeLinksByLessonVideo(int $lessonVideoId): Collection
    {
        return YoutubeLinkVideo::onlyTrashed()
            ->where('lesson_video_id', $lessonVideoId)
            ->orderByDesc('deleted_at')
            ->get();
    }

    public function getLessonVideoById(int $id): LessonVideo
    {
        return LessonVideo::findOrFail($id);
    }

    public function getUnitById(int $id): Unit
    {
        return Unit::findOrFail($id);
    }

    public function getTeacherById(int $id): Teacher
    {
        return Teacher::findOrFail($id);
    }

    public function getSubjectVideoById(int $id): SubjectVideo
    {
        return SubjectVideo::findOrFail($id);
    }

    public function getLessonVideosByUnit(int $unitId): Collection
    {
        return LessonVideo::where('unit_id', $unitId)->orderBy('order')->get();
    }

    public function lessonVideoBelongsToContext(
        int $lessonVideoId,
        int $unitId,
        int $teacherId,
        int $subjectVideoId
    ): bool {
        return LessonVideo::where('id', $lessonVideoId)
            ->where('unit_id', $unitId)
            ->whereHas('unit', function ($query) use ($teacherId, $subjectVideoId) {
                $query->where('teacher_id', $teacherId)
                    ->whereHas('teacher.subjectVideos', fn ($subQuery) => $subQuery->where('subject_video_id', $subjectVideoId));
            })
            ->exists();
    }

    public function createYoutubeLinkVideo(array $data): YoutubeLinkVideo
    {
        $data['is_active'] = $data['is_active'] ?? true;

        $lessonVideo = LessonVideo::findOrFail($data['lesson_video_id']);
        $unit = Unit::findOrFail($lessonVideo->unit_id);
        $teacher = Teacher::findOrFail($unit->teacher_id);

        $videoTime = $data['video_time'] ?? '00:00:00';
        $teacher->estimation_time = Time::add($teacher->estimation_time, $videoTime);
        $teacher->save();

        return YoutubeLinkVideo::create($data);
    }

    public function updateYoutubeLinkVideo(int $id, array $data): YoutubeLinkVideo
    {
        $youtubeLinkVideo = YoutubeLinkVideo::findOrFail($id);

        // Use existing values if not provided
        $lessonVideoId = $data['lesson_video_id'] ?? $youtubeLinkVideo->lesson_video_id;
        $lessonVideo = LessonVideo::findOrFail($lessonVideoId);
        $unit = Unit::findOrFail($lessonVideo->unit_id);
        $teacher = Teacher::findOrFail($unit->teacher_id);

        $oldVideoTime = $youtubeLinkVideo->video_time ?? '00:00:00';
        $newVideoTime = $data['video_time'] ?? '00:00:00';

        // Subtract old, add new
        $teacher->estimation_time = Time::sub($teacher->estimation_time, $oldVideoTime);
        $teacher->estimation_time = Time::add($teacher->estimation_time, $newVideoTime);
        $teacher->save();

        // Update other fields (including is_active if provided)
        if (array_key_exists('is_active', $data)) {
            $data['is_active'] = (bool) $data['is_active'];
        }
        $youtubeLinkVideo->update($data);

        return $youtubeLinkVideo;
    }

    public function deleteYoutubeLinkVideo(int $id): bool
    {
        $youtubeLinkVideo = YoutubeLinkVideo::findOrFail($id);
        $lessonVideo = LessonVideo::findOrFail($youtubeLinkVideo->lesson_video_id);
        $unit = Unit::findOrFail($lessonVideo->unit_id);
        $teacher = Teacher::findOrFail($unit->teacher_id);

        $videoTime = $youtubeLinkVideo->video_time ?? '00:00:00';
        $teacher->estimation_time = Time::sub($teacher->estimation_time, $videoTime);
        $teacher->save();

        return (bool) $youtubeLinkVideo->delete();
    }

    public function restoreYoutubeLinkVideo(int $id): void
    {
        $youtubeLinkVideo = YoutubeLinkVideo::onlyTrashed()->findOrFail($id);
        $youtubeLinkVideo->restore();

        $lessonVideo = LessonVideo::findOrFail($youtubeLinkVideo->lesson_video_id);
        $unit = Unit::findOrFail($lessonVideo->unit_id);
        $teacher = Teacher::findOrFail($unit->teacher_id);

        $videoTime = $youtubeLinkVideo->video_time ?? '00:00:00';
        $teacher->estimation_time = Time::add($teacher->estimation_time, $videoTime);
        $teacher->save();
    }

    public function forceDeleteYoutubeLinkVideo(int $id): void
    {
        $youtubeLinkVideo = YoutubeLinkVideo::onlyTrashed()->findOrFail($id);
        $youtubeLinkVideo->forceDelete();
    }

    public function toggleYoutubeLinkVideo(int $youtubeLinkVideoId): bool
    {
        $youtubeLinkVideo = YoutubeLinkVideo::findOrFail($youtubeLinkVideoId);
        return $youtubeLinkVideo->update(['is_active' => !$youtubeLinkVideo->is_active]);
    }

}
