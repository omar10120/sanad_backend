<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Http\Requests\LessonVideo\DeleteLessonVideoRequest;
use App\Http\Requests\LessonVideo\ReorderLessonVideoRequest;
use App\Http\Requests\LessonVideo\StoreLessonVideoRequest;
use App\Http\Requests\LessonVideo\UpdateLessonVideoRequest;
use App\Models\LessonVideo;
use App\Models\Unit;
use App\Services\LessonVideoService;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\Request;

class LessonVideoController extends Controller
{
    use HasPermissionChecks;

    protected LessonVideoService $lessonVideoService;

    public function __construct(LessonVideoService $lessonVideoService)
    {
        $this->lessonVideoService = $lessonVideoService;
    }

    public function index(Request $request)
    {
        $this->checkPermission(PermissionEnum::LESSON_VIDEO_SHOW);

        return redirect()->route('course-type.index');
    }

    public function byUnit(Request $request, $unit_id)
    {
        $this->checkPermission(PermissionEnum::LESSON_VIDEO_SHOW);

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
        $lessonVideos = $this->lessonVideoService->getLessonVideosByUnit($unit_id);
        $units = $this->lessonVideoService->getUnitsByTeacher($teacher_id);
        $archivedLessonVideosCount = $this->lessonVideoService->getArchivedLessonVideosCount($unit_id);

        return view('setting.lesson-video.lessons-video', compact(
            'lessonVideos',
            'units',
            'unit_selected',
            'teacher_selected',
            'subject_video_selected',
            'archivedLessonVideosCount',
        ));
    }

    public function store(StoreLessonVideoRequest $request)
    {
        $this->checkPermission(PermissionEnum::LESSON_VIDEO_ADD);

        $this->lessonVideoService->createLessonVideo($request->validated());

        session()->flash('add', trans('main_trans.Lesson_video_add_successfully'));
        return back();
    }

    public function update(UpdateLessonVideoRequest $request)
    {
        $this->checkPermission(PermissionEnum::LESSON_VIDEO_EDIT);

        $data = $request->validated();
        $this->lessonVideoService->updateLessonVideo($data['id'], [
            'title' => $data['title'],
            'unit_id' => $data['unit_id'],
        ]);

        session()->flash('edit', trans('main_trans.Lesson_video_edit_successfully'));
        return back();
    }

    public function destroy(DeleteLessonVideoRequest $request)
    {
        $this->checkPermission(PermissionEnum::LESSON_VIDEO_DELETE);

        $result = $this->lessonVideoService->deleteLessonVideo($request->id);

        if ($result['success']) {
            session()->flash('delete', $result['message']);
        } else {
            session()->flash('error', $result['message']);
        }

        return back();
    }

    public function reorder(ReorderLessonVideoRequest $request, Unit $unit)
    {
        $this->checkPermission(PermissionEnum::LESSON_VIDEO_EDIT);

        $lessonVideo = new LessonVideo();
        $lessonVideo->unit_id = $unit->id;
        $lessonVideo->updateOrder($request->ordered_ids);

        return response()->json(['success' => true]);
    }
}
