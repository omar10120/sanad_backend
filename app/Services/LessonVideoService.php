<?php

namespace App\Services;

use App\Models\LessonVideo;
use App\Models\SubjectVideo;
use App\Models\Teacher;
use App\Models\Unit;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
class LessonVideoService
{
    public function getLessonVideosByUnit(int $unitId): Collection
    {
        return LessonVideo::where('unit_id', $unitId)
            ->withCount('youtubeLinks')
            ->orderBy('order')
            ->get();
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

    public function getUnitsByTeacher(int $teacherId): Collection
    {
        return Unit::where('teacher_id', $teacherId)->orderBy('order')->get();
    }

    public function unitBelongsToContext(int $unitId, int $teacherId, int $subjectVideoId): bool
    {
        return Unit::where('id', $unitId)
            ->where('teacher_id', $teacherId)
            ->whereHas('teacher.subjectVideos', fn ($query) => $query->where('subject_video_id', $subjectVideoId))
            ->exists();
    }

    public function getArchivedLessonVideosByUnit(int $unitId): Collection
    {
        return LessonVideo::onlyTrashed()
            ->where('unit_id', $unitId)
            ->withCount('youtubeLinks')
            ->orderBy('order')
            ->get();
    }

    public function getArchivedLessonVideosCount(int $unitId): int
    {
        return LessonVideo::onlyTrashed()->where('unit_id', $unitId)->count();
    }

    public function createLessonVideo(array $data): LessonVideo
    {
        $data['is_active'] = array_key_exists('is_active', $data)
            ? (bool) $data['is_active']
            : true;

        return LessonVideo::create($data);
    }

    public function updateLessonVideo(int $id, array $data): LessonVideo
    {
        $lessonVideo = LessonVideo::findOrFail($id);

        if (array_key_exists('is_active', $data)) {
            $data['is_active'] = (bool) $data['is_active'];
        }

        $lessonVideo->update($data);

        return $lessonVideo;
    }

    public function deleteLessonVideo(int $id): array
    {
        $lessonVideo = LessonVideo::with(['youtubeLinks'])->findOrFail($id);

        try {
            DB::transaction(function () use ($lessonVideo) {
                $lessonVideo->youtubeLinks()->delete();
                $lessonVideo->delete();
            });

            return [
                'success' => true,
                'message' => trans('main_trans.Lesson_video_delete_successfully'),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function restoreLessonVideo(int $id): void
    {
        $lessonVideo = LessonVideo::onlyTrashed()->findOrFail($id);
        $lessonVideo->restore();
    }

    public function forceDeleteLessonVideo(int $id): void
    {
        $lessonVideo = LessonVideo::onlyTrashed()->findOrFail($id);
        $lessonVideo->forceDelete();
    }

    public function toggleLessonVideo(int $lessonVideoId): bool
    {
        $lessonVideo = LessonVideo::findOrFail($lessonVideoId);

        return $lessonVideo->update(['is_active' => ! $lessonVideo->is_active]);
    }
}
