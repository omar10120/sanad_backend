<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Http\Requests\Student\ForceDeleteStudentRequest;
use App\Http\Requests\Student\RestoreStudentRequest;
use App\Services\StudentService;
use App\Traits\HasPermissionChecks;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ArchivedStudentController extends Controller
{
    use HasPermissionChecks;

    protected StudentService $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    /**
     * @throws PermissionException
     */
    public function index(Request $request): Factory|Application|View
    {
        $this->checkPermission(PermissionEnum::STUDENT_SHOW_DELETED);

        $data = $this->studentService->getArchivedStudents();

        return view('student.students-deleted', compact('data'));
    }

    /**
     * @throws PermissionException
     */
    public function show(Request $request, $id): Factory|Application|View
    {
        $this->checkPermission(PermissionEnum::STUDENT_SHOW_DELETED);

        $data = $this->studentService->getArchivedStudents();

        return view('student.students-deleted', compact('data'));
    }

    /**
     * @throws PermissionException
     */
    public function update(RestoreStudentRequest $request): RedirectResponse
    {
        $this->checkPermission(PermissionEnum::STUDENT_RESTORE_DELETED);

        $this->studentService->restoreStudent($request->id);

        session()->flash('restore', trans('main_trans.Student_restore_successfully'));

        return back();
    }

    /**
     * @throws PermissionException
     */
    public function destroy(ForceDeleteStudentRequest $request): RedirectResponse
    {
        $this->checkPermission(PermissionEnum::STUDENT_DELETE);

        $this->studentService->forceDeleteStudent($request->id);

        session()->flash('delete', trans('main_trans.Student_delete_successfully'));

        return back();
    }
}
