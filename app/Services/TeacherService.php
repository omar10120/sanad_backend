<?php

namespace App\Services;

use App\Models\SubjectVideo;
use App\Models\Teacher;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TeacherService
{
    public function getSubjectVideoWithTeachers(int $subjectVideoId): SubjectVideo
    {
        return SubjectVideo::with(['teachers.units', 'types'])->findOrFail($subjectVideoId);
    }

    public function getAllSubjectVideos(): Collection
    {
        return SubjectVideo::orderBy('order')->get();
    }

    public function createTeacher(array $data, $photoFile = null): Teacher
    {
        $subjectVideoId = (int) $data['subject_video_id'];
        $subjectVideos = $data['subject_videos'] ?? [$subjectVideoId];
        unset($data['subject_video_id'], $data['subject_videos']);

        $teacher = Teacher::create($data);

        if ($photoFile) {
            $fileName = $this->handlePhotoUpload($teacher, $photoFile);
            if ($fileName) {
                $teacher->update(['photo' => $fileName]);
            }
        }

        $this->syncSubjectVideos($teacher, $subjectVideos);

        return $teacher->fresh();
    }

    public function updateTeacher(int $id, array $data, $photoFile = null): Teacher
    {
        $teacher = Teacher::findOrFail($id);

        if ($photoFile) {
            if ($teacher->photo) {
                $this->deleteTeacherPhoto($teacher->id, $teacher->photo);
            }
            $fileName = $this->handlePhotoUpload($teacher, $photoFile);
            if ($fileName) {
                $data['photo'] = $fileName;
            }
        }

        $subjectVideos = $data['subject_videos'] ?? null;
        unset($data['subject_videos'], $data['subject_video_id'], $data['id']);

        $teacher->update($data);

        if (is_array($subjectVideos)) {
            $this->syncSubjectVideos($teacher, $subjectVideos);
        }

        return $teacher->fresh();
    }

    public function deleteTeacher(int $id): array
    {
        $teacher = Teacher::findOrFail($id);

        if (!$teacher->canBeDeleted()) {
            return [
                'success' => false,
                'message' => trans('main_trans.Teacher_has_related_data'),
            ];
        }

        try {
            $teacher->delete();
            return [
                'success' => true,
                'message' => trans('main_trans.Teacher_delete_successfully'),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function getArchivedTeachers(int $subjectVideoId): Collection
    {
        return Teacher::onlyTrashed()
            ->whereHas('subjectVideos', function ($query) use ($subjectVideoId) {
                $query->where('subject_video_id', $subjectVideoId);
            })
            ->get();
    }

    public function restoreTeacher(int $id): void
    {
        Teacher::onlyTrashed()->findOrFail($id)->restore();
    }

    public function forceDeleteTeacher(int $id): void
    {
        $teacher = Teacher::onlyTrashed()->findOrFail($id);

        if ($teacher->photo) {
            $this->deleteTeacherPhoto($teacher->id, $teacher->photo);
        }

        $teacher->forceDelete();
    }

    public function handlePhotoUpload(Teacher $teacher, $photoFile): ?string
    {
        if (!$photoFile || !$photoFile->isValid()) {
            return null;
        }

        $extension = $photoFile->getClientOriginalExtension();
        $newFileName = 'teacher-' . $teacher->id . '-' . Carbon::now()->format('Ymd_His') . '.' . $extension;
        $uploadPath = public_path('assets/image/Teachers/' . $teacher->id);

        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        try {
            $photoFile->move($uploadPath, $newFileName);
            return $newFileName;
        } catch (Exception $e) {
            Log::error('Teacher photo upload failed: ' . $e->getMessage());
            return null;
        }
    }

    public function deleteTeacherPhoto(int $teacherId, string $photoName): bool
    {
        $path = public_path('assets/image/Teachers/' . $teacherId . '/' . $photoName);
        if (file_exists($path)) {
            return unlink($path);
        }
        return false;
    }

    private function attachToSubjectVideo(Teacher $teacher, int $subjectVideoId): void
    {
        $maxOrder = DB::table('teacher_has_subject_video')
            ->where('subject_video_id', $subjectVideoId)
            ->max('order') ?? 0;

        if (!$teacher->subjectVideos()->where('subject_video_id', $subjectVideoId)->exists()) {
            $teacher->subjectVideos()->attach($subjectVideoId, ['order' => $maxOrder + 1]);
        }
    }

    private function syncSubjectVideos(Teacher $teacher, array $subjectVideoIds): void
    {
        $syncData = [];
        foreach ($subjectVideoIds as $index => $subjectVideoId) {
            $existingOrder = DB::table('teacher_has_subject_video')
                ->where('teacher_id', $teacher->id)
                ->where('subject_video_id', $subjectVideoId)
                ->value('order');

            $syncData[$subjectVideoId] = ['order' => $existingOrder ?? ($index + 1)];
        }

        $teacher->subjectVideos()->sync($syncData);
    }
}
