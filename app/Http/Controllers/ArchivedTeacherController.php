<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Http\Requests\Teacher\ForceDeleteTeacherRequest;
use App\Http\Requests\Teacher\RestoreTeacherRequest;
use App\Services\TeacherService;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\Request;

class ArchivedTeacherController extends Controller
{
    use HasPermissionChecks;

    protected TeacherService $teacherService;

    public function __construct(TeacherService $teacherService)
    {
        $this->teacherService = $teacherService;
    }

    public function show(Request $request, $subject_video_id)
    {
        $this->checkPermission(PermissionEnum::TEACHER_SHOW_DELETED);

        $teachers = $this->teacherService->getArchivedTeachers($subject_video_id);
        $subject_video_selected = \App\Models\SubjectVideo::with('types')->findOrFail($subject_video_id);

        return view('setting.teacher.teachers-deleted', compact(
            'teachers',
            'subject_video_selected',
        ));
    }

    public function update(RestoreTeacherRequest $request)
    {
        $this->checkPermission(PermissionEnum::TEACHER_RESTORE_DELETED);

        $this->teacherService->restoreTeacher($request->id);
        session()->flash('restore', trans('main_trans.Teacher_restore_successfully'));
        return back();
    }

    public function destroy(ForceDeleteTeacherRequest $request)
    {
        $this->checkPermission(PermissionEnum::TEACHER_DELETE);

        $this->teacherService->forceDeleteTeacher($request->id);
        session()->flash('delete', trans('main_trans.Teacher_delete_successfully'));
        return back();
    }
}
