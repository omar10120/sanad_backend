<?php

namespace App\Services;

use App\Models\SubjectVideo;
use App\Models\Teacher;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
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
        $data['estimation_time'] = $data['estimation_time'] ?? 0;
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
        $teacher->estimation_time = $data['estimation_time'] ?? 0;
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

        try {
            // Soft-delete only — keep subject-video pivots and units so restore works.
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
            ->where(function ($query) use ($subjectVideoId) {
                $query->whereHas('subjectVideos', function ($q) use ($subjectVideoId) {
                    $q->where('subjects_video.id', $subjectVideoId);
                })->orWhereDoesntHave('subjectVideos');
            })
            ->orderByDesc('deleted_at')
            ->get();
    }

    public function restoreTeacher(int $id, ?int $subjectVideoId = null): void
    {
        $teacher = Teacher::onlyTrashed()->findOrFail($id);
        $teacher->restore();

        // Re-attach if pivot was removed by older soft-delete logic.
        if ($subjectVideoId) {
            $this->attachToSubjectVideo($teacher, $subjectVideoId);
        }
    }

    public function forceDeleteTeacher(int $id): void
    {
        $teacher = Teacher::onlyTrashed()
            ->with([
                'units.lessonVideos' => fn ($query) => $query->withTrashed()->with('youtubeLinks'),
            ])
            ->findOrFail($id);

        DB::transaction(function () use ($teacher) {
            foreach ($teacher->units as $unit) {
                foreach ($unit->lessonVideos as $lessonVideo) {
                    $lessonVideo->youtubeLinks()->delete();
                    $lessonVideo->forceDelete();
                }

                $unit->delete();
            }

            $teacher->subjectVideos()->detach();

            if ($teacher->photo) {
                $this->deleteTeacherPhoto($teacher->id, $teacher->photo);
            }

            $teacher->forceDelete();
        });
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

    public function toggleTeacher(int $teacherId): bool
    {
        $teacher = Teacher::findOrFail($teacherId);

        return $teacher->update(['is_active' => ! $teacher->is_active]);
    }

    /**
     * Teacher IDs for the authenticated user.
     * null  → show all (Owner or show_all_teachers)
     * []    → show none
     * [ids] → show only those teachers
     */
    public function getRestrictedTeacherIdsForUser(): ?array
    {
        $user = Auth::user();

        if (! $user) {
            return [];
        }

        if ($user->hasRole('Owner') || $user->show_all_teachers) {
            return null;
        }

        if ($user->teacher_id) {
            return [(int) $user->teacher_id];
        }

        return [];
    }

    /**
     * Teachers available to the authenticated user.
     */
    public function getTeachersForUser(): Collection
    {
        $restrictedIds = $this->getRestrictedTeacherIdsForUser();

        if ($restrictedIds === null) {
            return Teacher::orderBy('name')->get();
        }

        if ($restrictedIds === []) {
            return new Collection();
        }

        return Teacher::whereIn('id', $restrictedIds)->orderBy('name')->get();
    }

    /**
     * Teachers for a subject video, limited to the authenticated user's assigned teachers.
     */
    public function getTeachersForSubjectVideoForUser(int $subjectVideoId): Collection
    {
        $subjectVideo = $this->getSubjectVideoWithTeachers($subjectVideoId);
        $teachers = $subjectVideo->teachers;

        $restrictedIds = $this->getRestrictedTeacherIdsForUser();

        if ($restrictedIds === null) {
            return $teachers;
        }

        return $teachers->whereIn('id', $restrictedIds)->values();
    }
}
