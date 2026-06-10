<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Traits\HasPermissionChecks;
use App\Services\StudentService;
use App\Http\Requests\Student\StoreStudentRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class StudentController extends Controller
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
    public function index(Request $request): View|Application
    {
        $this->checkPermission(PermissionEnum::STUDENT_SHOW);

        $data = $this->studentService->getStudentsByAcademicYear('2024-2025');
        return view('student.show-students', compact('data'));
    }

    /**
     * @throws PermissionException
     */
    public function currentAcademicYear(Request $request): View|Application
    {
        $this->checkPermission(PermissionEnum::STUDENT_SHOW);

        $data = $this->studentService->getStudentsByAcademicYear('2025-2026');
        return view('student.current-academic-year-students', compact('data'));
    }

    /**
     * @throws PermissionException
     */
    public function create(Request $request): View|Application
    {
        $this->checkPermission(PermissionEnum::STUDENT_ADD);

        $types = $this->studentService->getAllTypes();
        return view('student.add-student',compact(
            'types',
        ));
    }

    /**
     * @throws PermissionException
     */
    public function store(StoreStudentRequest $request): Application|Redirector|RedirectResponse
    {
        $this->checkPermission(PermissionEnum::STUDENT_ADD);

        $student = $this->studentService->createStudent($request->validated(), $request);

        session()->flash('add', trans('main_trans.Student_add_successfully') );
        return redirect('student');
    }

    /**
     * @throws PermissionException
     */
    public function show(Request $request, $id): View|Application
    {
        $this->checkPermission(PermissionEnum::STUDENT_SHOW);

        $student = $this->studentService->findStudent($id);
        return view('student.show-student',compact('student'));
    }

    /**
     * @throws PermissionException
     */
    public function edit(Request $request, $id): View|Application
    {
        $this->checkPermission(PermissionEnum::STUDENT_EDIT);

        $student = $this->studentService->findStudent($id);
        $types = $this->studentService->getAllTypes();
//        $roles = Role::pluck('name','name')->all();
//        $userRole = $user->roles->pluck('name','name')->all();

        return view('student.edit-student',compact('student',
            'types',
        ));
    }

    /**
     * @throws PermissionException
     */
    public function update(UpdateStudentRequest $request, $id): Redirector|RedirectResponse
    {
        $this->checkPermission(PermissionEnum::STUDENT_EDIT);

        $student = $this->studentService->findStudent($id);
        $this->studentService->updateStudent($student, $request->validated(), $request);

        session()->flash('edit', trans('main_trans.Student_edit_successfully') );
        return redirect('student');
    }

    /**
     * @throws PermissionException
     */
    public function destroy(Request $request): RedirectResponse
    {
        $this->checkPermission(PermissionEnum::STUDENT_DELETE);

        $this->studentService->deleteStudent($request->id);
        session()->flash('delete', trans('main_trans.Student_delete_successfully') );
        return back();
    }

    /**
     * @throws PermissionException
     */
    public function deleteDeviceId($id): RedirectResponse
    {
        $this->checkPermission(PermissionEnum::STUDENT_EDIT);

        $this->studentService->deleteDeviceId($id);
        session()->flash('edit', trans('main_trans.Device_id_deleted_successfully'));
        return back();
    }

    /**
     * Update device limit for a student
     *
     * @throws PermissionException
     */
    public function updateDeviceLimit(Request $request, $id): RedirectResponse
    {
        $this->checkPermission(PermissionEnum::STUDENT_EDIT);

        $request->validate([
            'max_devices' => 'required|integer|min:1|max:10'
        ]);

        $student = $this->studentService->findStudent($id);
        $student->update(['max_devices' => $request->max_devices]);

        session()->flash('edit', trans('main_trans.Device_limit_updated_successfully'));
        return back();
    }

    /**
     * Remove a device from a student
     *
     * @throws PermissionException
     */
    public function removeDevice(Request $request, $studentId, $deviceId): RedirectResponse
    {
        $this->checkPermission(PermissionEnum::STUDENT_EDIT);

        $student = $this->studentService->findStudent($studentId);
        $studentDevice = $student->studentDevices()->where('device_id', $deviceId)->first();

        if ($studentDevice) {
            $studentDevice->delete();
            session()->flash('edit', trans('main_trans.Device_removed_successfully'));
        }

        return back();
    }

    /**
     * Get devices for a student (AJAX)
     *
     * @throws PermissionException
     */
    public function getDevices($id): View|Application
    {
        $this->checkPermission(PermissionEnum::STUDENT_SHOW);

        $student = $this->studentService->findStudent($id);
        $devices = $student->studentDevices()->with('device')->get();

        return view('student.partials.devices', compact('devices', 'student'));
    }
}
