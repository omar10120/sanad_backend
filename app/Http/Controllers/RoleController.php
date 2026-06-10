<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Services\RoleService;
use App\Traits\HasPermissionChecks;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    use HasPermissionChecks;

    protected RoleService $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * @throws PermissionException
     */
    public function index(Request $request)
    {
        $this->checkPermission(PermissionEnum::ROLE_SHOW);

        $roles = $this->roleService->getAllRoles();
        return view('roles.index', compact('roles'));
    }

    /**
     * @throws PermissionException
     */
    public function create()
    {
        $this->checkPermission(PermissionEnum::ROLE_ADD);
        $permission = $this->roleService->getAllPermissions();
        return view('roles.create', compact('permission'));
    }

    /**
     * @throws PermissionException
     */
    public function store(StoreRoleRequest $request)
    {
        $this->checkPermission(PermissionEnum::ROLE_ADD);

        $validatedData = $request->validated();
        $roleData = ['name' => $validatedData['name']];
        $permissionIds = $validatedData['permission'] ?? null;

        $this->roleService->createRole($roleData, $permissionIds);

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully');
    }

    /**
     * @throws PermissionException
     */
    public function show(Request $request, $id)
    {
        $this->checkPermission(PermissionEnum::ROLE_SHOW);

        $role = $this->roleService->findRole($id);
        $rolePermissions = $this->roleService->getRolePermissions($id);

        return view('roles.show', compact('role', 'rolePermissions'));
    }

    /**
     * @throws PermissionException
     */
    public function edit(Request $request, $id)
    {
        $this->checkPermission(PermissionEnum::ROLE_EDIT);

        $role = $this->roleService->findRole($id);
        $permission = $this->roleService->getAllPermissions();
        $rolePermissions = $this->roleService->getRolePermissionIds($id);

        return view('roles.edit', compact('role', 'permission', 'rolePermissions'));
    }

    /**
     * @throws PermissionException
     */
    public function update(UpdateRoleRequest $request, $id)
    {
        $this->checkPermission(PermissionEnum::ROLE_EDIT);

        $role = $this->roleService->findRole($id);
        $validatedData = $request->validated();
        $roleData = ['name' => $validatedData['name']];
        $permissionIds = $validatedData['permission'] ?? null;

        $this->roleService->updateRole($role, $roleData, $permissionIds);

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully');
    }

    /**
     * @throws PermissionException
     */
    public function destroy(Request $request, $id)
    {
        $this->checkPermission(PermissionEnum::ROLE_DELETE);

        $this->roleService->deleteRole($id);

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully');
    }
}
