<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Http\Requests\User\DeleteUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Services\UserService;
use App\Services\UserSubjectService;
use App\Traits\HasPermissionChecks;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
class UserController extends Controller
{
    use HasPermissionChecks;

    protected UserService $userService;
    protected UserSubjectService $userSubjectService;

    public function __construct(UserService $userService, UserSubjectService $userSubjectService)
    {
        $this->userService = $userService;
        $this->userSubjectService = $userSubjectService;
    }

    /**
     * @throws PermissionException
     */
    public function index(Request $request): Factory|Application|View
    {
        $this->checkPermission(PermissionEnum::USER_SHOW);

        $data = $this->userService->getAllUsers();
        return view('users.show_users', compact('data'));
    }

    /**
     * @throws PermissionException
     */
    public function create(Request $request): Factory|Application|View
    {
        $this->checkPermission(PermissionEnum::USER_ADD);

        $roles = $this->userService->getAllRoles();
        $availableSubjects = $this->userSubjectService->getAllSubjects();
        $availableTeachers = $this->userSubjectService->getAllTeachers();
        
        return view('users.add_user', compact('roles', 'availableSubjects', 'availableTeachers'));
    }

    /**
     * @throws PermissionException
     */
    public function store(StoreUserRequest $request): Application|Redirector|RedirectResponse
    {
        $this->checkPermission(PermissionEnum::USER_ADD);

        // Prepare user data
        $userData = $this->userService->prepareUserData($request);

        // Handle file upload
        $photoName = $this->userService->handleFileUpload($request);

        // Create user
        $user = $this->userService->createUser($userData, $photoName, $request->roles_name[0]);

        // Move uploaded file if exists
        if ($photoName) {
            $this->userService->moveUploadedFile($request, $user->id, $photoName);
        }

        // Assign subjects and teacher if provided and user is not Owner
        if ($request->filled('subjects') && ! $user->hasRole('Owner')) {
            $subjectIds = $request->input('subjects', []);
            $teacherId = $request->input('teacher_id') ?: null;
            $this->userSubjectService->assignSubjectsToUser(
                $user->id,
                $subjectIds,
                $teacherId ? (int) $teacherId : null
            );
        }

        session()->flash('add', trans('main_trans.User_add_successfully'));
        return redirect('users');
    }

    /**
     * @throws PermissionException
     */
    public function show(Request $request, $id): Factory|Application|View
    {
        $this->checkPermission(PermissionEnum::USER_SHOW);

        $user = $this->userService->findUser($id);
        return view('users.show', compact('user'));
    }

    /**
     * @throws PermissionException
     */
    public function edit(Request $request, $id): Factory|Application|View
    {
        $this->checkPermission(PermissionEnum::USER_EDIT);

        $user = $this->userService->findUser($id);
        $roles = $this->userService->getAllRoles();
        $userRole = $this->userService->getUserRoles($user);
        $assignedSubjects = $this->userSubjectService->getUserSubjects($id);
        $availableSubjects = $this->userSubjectService->getAllSubjects();
        $availableTeachers = $this->userSubjectService->getAllTeachers();
        $assignedTeacherId = $this->userSubjectService->getUserTeacherId($id);

        return view('users.edit_user', compact(
            'user',
            'roles',
            'userRole',
            'assignedSubjects',
            'availableSubjects',
            'availableTeachers',
            'assignedTeacherId'
        ));
    }

    /**
     * @throws PermissionException
     */
    public function update(UpdateUserRequest $request, $id): Application|Redirector|RedirectResponse
    {
        $this->checkPermission(PermissionEnum::USER_EDIT);

        $user = $this->userService->findUser($id);

        // Update user photo if provided
        $this->userService->updateUserPhoto($request, $user);

        // Update user data
        $userData = $this->userService->prepareUserUpdateData($request);
        $this->userService->updateUser($user, $userData);

        // Update user role
        $this->userService->updateUserRole($user, $request->roles_name[0]);

        // Update subject assignments and teacher if provided and user is not Owner
        if ($request->filled('subjects') && ! $user->hasRole('Owner')) {
            $subjectIds = $request->input('subjects', []);
            $teacherId = $request->input('teacher_id') ?: null;
            $this->userSubjectService->assignSubjectsToUser(
                $user->id,
                $subjectIds,
                $teacherId ? (int) $teacherId : null
            );
        }

        session()->flash('edit', trans('main_trans.User_edit_successfully'));
        return redirect('users');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $user = $this->userService->findUser($request->id);
        $this->userService->updateUserPassword($user, $request->password);

        session()->flash('edit', trans('main_trans.User_edit_successfully'));
        return back();
    }

    public function profile(): Factory|Application|View
    {
        return view('users.profile');
    }

    /**
     * @throws PermissionException
     */
    public function destroy(DeleteUserRequest $request): RedirectResponse
    {
        $this->checkPermission(PermissionEnum::USER_DELETE);

        $this->userService->deleteUser($request->id);

        session()->flash('delete', trans('main_trans.User_delete_successfully'));
        return back();
    }
}
