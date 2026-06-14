<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Http\Requests\LessonVideo\ForceDeleteLessonVideoRequest;
use App\Http\Requests\LessonVideo\RestoreLessonVideoRequest;
use App\Services\LessonVideoService;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\Request;

class ArchivedLessonVideoController extends Controller
{
    use HasPermissionChecks;

    protected LessonVideoService $lessonVideoService;

    public function __construct(LessonVideoService $lessonVideoService)
    {
        $this->lessonVideoService = $lessonVideoService;
    }

    public function show(Request $request, $unit_id)
    {
        $this->checkPermission(PermissionEnum::LESSON_VIDEO_SHOW_DELETED);

        $subject_video_id = $request->query('subject_video');
        $teacher_id = $request->query('teacher');

        if (!$subject_video_id || !$teacher_id) {
            abort(404);
        }

        if (!$this->lessonVideoService->unitBelongsToContext($unit_id, $teacher_id, $subject_video_id)) {
            abort(404);
        }

        $unit_selected = $this->lessonVideoService->getUnitById($unit_id);
        $teacher_selected = $this->lessonVideoService->getTeacherById($teacher_id);
        $subject_video_selected = $this->lessonVideoService->getSubjectVideoById($subject_video_id);
        $lessonVideos = $this->lessonVideoService->getArchivedLessonVideosByUnit($unit_id);

        return view('setting.lesson-video.lessons-video-deleted', compact(
            'lessonVideos',
            'unit_selected',
            'teacher_selected',
            'subject_video_selected',
        ));
    }

    public function update(RestoreLessonVideoRequest $request)
    {
        $this->checkPermission(PermissionEnum::LESSON_VIDEO_RESTORE_DELETED);

        $this->lessonVideoService->restoreLessonVideo($request->id);
        session()->flash('restore', trans('main_trans.Lesson_video_restore_successfully'));
        return back();
    }

    public function destroy(ForceDeleteLessonVideoRequest $request)
    {
        $this->checkPermission(PermissionEnum::LESSON_VIDEO_DELETE);

        $this->lessonVideoService->forceDeleteLessonVideo($request->id);
        session()->flash('delete', trans('main_trans.Lesson_video_delete_successfully'));
        return back();
    }
}
