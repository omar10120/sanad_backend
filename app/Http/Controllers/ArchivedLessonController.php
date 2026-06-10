<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Traits\HasPermissionChecks;
use App\Models\Subject;
use App\Services\LessonService;
use App\Http\Requests\Lesson\RestoreLessonRequest;
use App\Http\Requests\Lesson\ForceDeleteLessonRequest;
use Illuminate\Http\Request;

class ArchivedLessonController extends Controller
{
    use HasPermissionChecks;

    protected $lessonService;

    public function __construct(LessonService $lessonService)
    {
        $this->lessonService = $lessonService;
    }

    public function show(Request $request, $subject_id)
    {
        $this->checkPermission(PermissionEnum::LESSON_SHOW_DELETED);

        $lessons = $this->lessonService->getArchivedLessons($subject_id);
        $subject_selected = Subject::where('id',$subject_id)->first();
        $subjects = $this->lessonService->getAllSubjects();
        return view('setting.lesson.lessons-deleted', compact(
            'lessons',
            'subjects',
            'subject_selected',
        ));
    }

    public function update(RestoreLessonRequest $request)
    {
        $this->checkPermission(PermissionEnum::LESSON_RESTORE_DELETED);

        $this->lessonService->restoreLesson($request->id);
        session()->flash('restore', trans('main_trans.Lesson_restore_successfully') );
        return back();
    }

    public function destroy(ForceDeleteLessonRequest $request)
    {
        $this->checkPermission(PermissionEnum::LESSON_DELETE);

        $this->lessonService->forceDeleteLesson($request->id);
        session()->flash('delete', trans('main_trans.Lesson_delete_successfully') );
        return back();
    }
}
