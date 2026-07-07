<?php

namespace App\Services;

use App\Models\SubjectVideo;
use App\Models\Teacher;
use App\Models\Unit;
use App\Models\LessonVideo;
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
        return Unit::create($data);
    }

    public function updateUnit(int $id, array $data): ?Unit
    {
        $unit = Unit::findOrFail($id);
        $unit->update($data);

        return $unit;
    }

    
    public function deleteUnit(int $id): array
    {
        $unit = Unit::with(['lessonVideos.youtubeLinks'])->findOrFail($id);

        try {
            DB::transaction(function () use ($unit) {
                $this->deleteUnitWithRelations($unit);
            });

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

    private function deleteUnitWithRelations(Unit $unit): void
    {
        foreach ($unit->lessonVideos as $lessonVideo) {
            $this->deleteLessonVideoWithRelations($lessonVideo);
        }

        $unit->delete();
    }



    private function deleteLessonVideoWithRelations(LessonVideo $lessonVideo): void
    {
        $lessonVideo->youtubeLinks()->delete();
        $lessonVideo->delete();
    }
}
