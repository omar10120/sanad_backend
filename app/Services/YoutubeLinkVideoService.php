<?php

namespace App\Services;

use App\Models\LessonVideo;
use App\Models\SubjectVideo;
use App\Models\Teacher;
use App\Models\Unit;
use App\Models\YoutubeLinkVideo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
class YoutubeLinkVideoService
{
    public function getYoutubeLinksByLessonVideo(int $lessonVideoId): Collection
    {
        return YoutubeLinkVideo::where('lesson_video_id', $lessonVideoId)
            ->orderBy('order')
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
        $data['is_active'] = array_key_exists('is_active', $data)
            ? (bool) $data['is_active']
            : true;

        $lessonVideo = LessonVideo::findOrFail($data['lesson_video_id']);
        $unit = Unit::findOrFail($lessonVideo->unit_id);
        $teacher = Teacher::findOrFail($unit->teacher_id);
        $this->updateTeacherEstimationTime($teacher, (int) ($data['video_time'] ?? 0));

        return YoutubeLinkVideo::create($data);
    }

    public function updateYoutubeLinkVideo(int $id, array $data): YoutubeLinkVideo
    {
        if (array_key_exists('is_active', $data)) {
            $data['is_active'] = (bool) $data['is_active'];
        }

        $lessonVideo = LessonVideo::findOrFail($data['lesson_video_id']);
        $unit = Unit::findOrFail($lessonVideo->unit_id);
        $teacher = Teacher::findOrFail($unit->teacher_id);
        $youtubeLinkVideo = YoutubeLinkVideo::findOrFail($id);
        $teacher->estimation_time -= (int) ($youtubeLinkVideo->video_time ?? 0);
        $this->updateTeacherEstimationTime($teacher, (int) ($data['video_time'] ?? 0));
        $youtubeLinkVideo->update($data);

        return $youtubeLinkVideo;
    }

    public function deleteYoutubeLinkVideo(int $id): bool
    {
        $youtubeLinkVideo = YoutubeLinkVideo::findOrFail($id);
        $lessonVideo = LessonVideo::findOrFail($youtubeLinkVideo->lesson_video_id);
        $unit = Unit::findOrFail($lessonVideo->unit_id);
        $teacher = Teacher::findOrFail($unit->teacher_id);
        $teacher->estimation_time -= (int) ($youtubeLinkVideo->video_time ?? 0);
        $teacher->save();

        return (bool) $youtubeLinkVideo->delete();
    }

    public function toggleYoutubeLinkVideo(int $youtubeLinkVideoId): bool
    {
        $youtubeLinkVideo = YoutubeLinkVideo::findOrFail($youtubeLinkVideoId);

        return $youtubeLinkVideo->update(['is_active' => ! $youtubeLinkVideo->is_active]);
    }

    public function updateTeacherEstimationTime(Teacher $teacher, int $videoTime): void
    {
        $teacher->estimation_time += $videoTime;
        $teacher->save();
    }
}
