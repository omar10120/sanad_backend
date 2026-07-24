<?php

namespace App\Services;

use App\Models\LessonVideo;
use App\Models\SubjectVideo;
use App\Models\Teacher;
use App\Models\Unit;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class UnitService
{
    public function getUnitsByTeacher(int $teacherId): Collection
    {
        return Unit::where('teacher_id', $teacherId)
            ->withCount('lessonVideos')
            ->orderBy('order')
            ->get();
    }

    public function getArchivedUnitsByTeacher(int $teacherId): Collection
    {
        return Unit::onlyTrashed()
            ->where('teacher_id', $teacherId)
            ->withCount('lessonVideos')
            ->orderByDesc('deleted_at')
            ->get();
    }

    public function getTeacherById(int $id): Teacher
    {
        return Teacher::findOrFail($id);
    }

    public function getSubjectVideoById(int $id): SubjectVideo
    {
        return SubjectVideo::findOrFail($id);
    }

    public function getTeachersBySubjectVideo(int $subjectVideoId): Collection
    {
        $subjectVideo = SubjectVideo::with('teachers')->findOrFail($subjectVideoId);

        return $subjectVideo->teachers;
    }

    public function teacherBelongsToSubjectVideo(int $teacherId, int $subjectVideoId): bool
    {
        return Teacher::where('id', $teacherId)
            ->whereHas('subjectVideos', fn ($query) => $query->where('subject_video_id', $subjectVideoId))
            ->exists();
    }

    public function createUnit(array $data): Unit
    {
        $data['is_active'] = array_key_exists('is_active', $data)
            ? (bool) $data['is_active']
            : true;

        return Unit::create($data);
    }

    public function updateUnit(int $id, array $data): ?Unit
    {
        $unit = Unit::findOrFail($id);

        if (array_key_exists('is_active', $data)) {
            $data['is_active'] = (bool) $data['is_active'];
        }

        $unit->update($data);

        return $unit;
    }

    public function toggleUnit(int $unitId): bool
    {
        $unit = Unit::findOrFail($unitId);

        return $unit->update(['is_active' => ! $unit->is_active]);
    }

    public function deleteUnit(int $id): array
    {
        $unit = Unit::findOrFail($id);

        try {
            // Soft-delete only — keep lesson videos so restore works.
            $unit->delete();

            return [
                'success' => true,
                'message' => trans('main_trans.Unit_delete_successfully'),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function restoreUnit(int $id): void
    {
        Unit::onlyTrashed()->findOrFail($id)->restore();
    }

    public function forceDeleteUnit(int $id): void
    {
        $unit = Unit::onlyTrashed()
            ->with([
                'lessonVideos' => fn ($query) => $query->withTrashed()->with('youtubeLinks'),
            ])
            ->findOrFail($id);

        DB::transaction(function () use ($unit) {
            foreach ($unit->lessonVideos as $lessonVideo) {
                $this->forceDeleteLessonVideoWithRelations($lessonVideo);
            }

            $unit->forceDelete();
        });
    }

    private function forceDeleteLessonVideoWithRelations(LessonVideo $lessonVideo): void
    {
        $lessonVideo->youtubeLinks()->delete();
        $lessonVideo->forceDelete();
    }
}
