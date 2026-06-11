<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Http\Requests\User\ForceDeleteUserRequest;
use App\Http\Requests\User\RestoreUserRequest;
use App\Services\UserService;
use App\Traits\HasPermissionChecks;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ArchivedUserController extends Controller
{
    use HasPermissionChecks;

    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @throws PermissionException
     */
    public function index(Request $request): Factory|Application|View
    {
        $this->checkPermission(PermissionEnum::USER_SHOW_DELETED);

        $data = $this->userService->getArchivedUsers();

        return view('users.users-deleted', compact('data'));
    }

    /**
     * @throws PermissionException
     */
    public function show(Request $request, $id): Factory|Application|View
    {
        $this->checkPermission(PermissionEnum::USER_SHOW_DELETED);

        $data = $this->userService->getArchivedUsers();

        return view('users.users-deleted', compact('data'));
    }

    /**
     * @throws PermissionException
     */
    public function update(RestoreUserRequest $request): RedirectResponse
    {
        $this->checkPermission(PermissionEnum::USER_RESTORE_DELETED);

        $this->userService->restoreUser($request->id);

        session()->flash('restore', trans('main_trans.User_restore_successfully'));

        return back();
    }

    /**
     * @throws PermissionException
     */
    public function destroy(ForceDeleteUserRequest $request): RedirectResponse
    {
        $this->checkPermission(PermissionEnum::USER_DELETE);

        $this->userService->forceDeleteUser($request->id);

        session()->flash('delete', trans('main_trans.User_delete_successfully'));

        return back();
    }
}
