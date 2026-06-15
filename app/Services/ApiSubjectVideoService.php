<?php

namespace App\Services;

use App\Models\CodePackage;
use App\Models\LessonVideo;
use App\Models\SubjectVideo;
use App\Models\Type;
use App\Models\Unit;
use App\Models\YoutubeLinkVideo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class ApiSubjectVideoService
{
    public function getSubjectVideosForStudentType(int $typeId): Collection
    {
        return SubjectVideo::where('is_active', true)
            ->whereHas('types', fn ($query) => $query->where('types.id', $typeId))
            ->orderBy('order')
            ->get();
    }

    public function findSubjectVideo(int $id): ?SubjectVideo
    {
        return SubjectVideo::where('is_active', true)->find($id);
    }

    public function getTeachers(int $subjectVideoId): Collection
    {
        $subjectVideo = $this->findSubjectVideo($subjectVideoId);

        if (!$subjectVideo) {
            return collect();
        }

        return $subjectVideo->teachers()->withCount('units')->get();
    }

    public function studentHasAccess(SubjectVideo $subjectVideo, int $studentId): bool
    {
        return $subjectVideo->checkStudentAccess($studentId);
    }

    public function studentHasUnitAccess(int $studentId, int $unitId): bool
    {
        $student = Auth::user();

        if ($student && in_array($student->type_id, [7, 11], true)) {
            return true;
        }

        return CodePackage::where('expires_at', '>', now())
            ->whereHas('codes', fn ($query) => $query->where('student_id', $studentId))
            ->whereHas('codePackageSubjects', fn ($query) => $query->where('unit_id', $unitId))
            ->exists();
    }

    public function getStudentValidToDate(SubjectVideo $subjectVideo, int $studentId): ?string
    {
        $student = Auth::user();

        if ($student && in_array($student->type_id, [7, 11], true)) {
            return '2026-10-01';
        }

        $unitIds = Unit::whereHas('teacher.subjectVideos', function ($query) use ($subjectVideo) {
            $query->where('subjects_video.id', $subjectVideo->id);
        })->pluck('id');

        $validToDate = CodePackage::where('expires_at', '>', now())
            ->whereHas('codes', fn ($query) => $query->where('student_id', $studentId))
            ->whereHas('codePackageSubjects', fn ($query) => $query->whereIn('unit_id', $unitIds))
            ->max('expires_at');

        return $validToDate ? Carbon::parse($validToDate)->format('Y-m-d') : null;
    }

    public function getAllSubjectVideoData(int $subjectVideoId, bool $isLocked): array
    {
        $subjectVideo = SubjectVideo::with([
            'teachers.units.lessonVideos.youtubeLinks',
        ])->findOrFail($subjectVideoId);

        $teachers = $subjectVideo->teachers;
        $units = $teachers->flatMap->units->sortBy('order')->values();
        $lessonVideos = $units->flatMap->lessonVideos->sortBy('order')->values();

        if ($isLocked) {
            $firstUnit = $units->first();
            $firstLesson = $firstUnit?->lessonVideos->sortBy('order')->first();
            $lessonVideos = $firstLesson ? collect([$firstLesson]) : collect();
            $units = $firstUnit ? collect([$firstUnit]) : collect();
            $teachers = $teachers->take(1);
        }

        $youtubeLinks = $lessonVideos->flatMap(function (LessonVideo $lessonVideo) {
            return $lessonVideo->youtubeLinks;
        })->sortBy('order')->values();

        return [
            'subject_video' => $subjectVideo,
            'teachers' => $teachers,
            'units' => $units,
            'lesson_videos' => $lessonVideos,
            'youtube_links' => $youtubeLinks,
            'is_locked' => $isLocked,
        ];
    }

    public function getTypeWithSubjectVideos(int $typeId): ?Type
    {
        return Type::with(['subjectVideos' => function ($query) {
            $query->where('is_active', true)->orderBy('order');
        }])->find($typeId);
    }
}
