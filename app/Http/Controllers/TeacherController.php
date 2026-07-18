<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Http\Requests\Teacher\DeleteTeacherRequest;
use App\Http\Requests\Teacher\ReorderTeacherRequest;
use App\Http\Requests\Teacher\StoreTeacherRequest;
use App\Http\Requests\Teacher\UpdateTeacherRequest;
use App\Models\SubjectVideo;
use App\Services\TeacherService;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    use HasPermissionChecks;

    protected TeacherService $teacherService;

    public function __construct(TeacherService $teacherService)
    {
        $this->teacherService = $teacherService;
    }

    public function index(Request $request)
    {
        $this->checkPermission(PermissionEnum::TEACHER_SHOW);
        return redirect()->route('course-type.index');
    }

    public function bySubjectVideo(Request $request, $subject_video_id)
    {
        $this->checkPermission(PermissionEnum::TEACHER_SHOW);

        $subject_video_selected = $this->teacherService->getSubjectVideoWithTeachers($subject_video_id);
        $archivedTeachersCount = $this->teacherService->getArchivedTeachers($subject_video_id)->count();
        $teachers = $this->teacherService->getTeachersForSubjectVideoForUser((int) $subject_video_id);
        $subjectVideos = $this->teacherService->getAllSubjectVideos();

        return view('setting.teacher.teachers', compact(
            'teachers',
            'subjectVideos',
            'subject_video_selected',
            'archivedTeachersCount',
        ));
    }

    public function store(StoreTeacherRequest $request)
    {
        $this->checkPermission(PermissionEnum::TEACHER_ADD);

        $this->teacherService->createTeacher(
            $request->validated(),
            $request->file('photo')
        );

        session()->flash('add', trans('main_trans.Teacher_add_successfully'));
        return back();
    }

    public function update(UpdateTeacherRequest $request)
    {
        $this->checkPermission(PermissionEnum::TEACHER_EDIT);

        $data = $request->validated();
        $this->teacherService->updateTeacher(
            $data['id'],
            $data,
            $request->file('photo')
        );

        session()->flash('edit', trans('main_trans.Teacher_edit_successfully'));
        return back();
    }

    public function destroy(DeleteTeacherRequest $request)
    {
        $this->checkPermission(PermissionEnum::TEACHER_DELETE);

        $result = $this->teacherService->deleteTeacher($request->id);

        if ($result['success']) {
            session()->flash('delete', $result['message']);
        } else {
            session()->flash('error', $result['message']);
        }

        return back();
    }

    public function reorder(ReorderTeacherRequest $request, SubjectVideo $subjectVideo)
    {
        $this->checkPermission(PermissionEnum::TEACHER_EDIT);

        $subjectVideo->updateTeacherOrder($request->ordered_ids);

        return response()->json(['success' => true]);
    }
    public function toggle($teacher_id)
    {
        $this->checkPermission(PermissionEnum::TEACHER_EDIT);

        $this->teacherService->toggleTeacher((int) $teacher_id);

        return back();
    }
}
