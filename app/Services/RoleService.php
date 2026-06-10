<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleService
{
    /**
     * Get all roles with pagination
     *
     * @return Collection
     */
    public function getAllRoles(): Collection
    {
        return Role::all();
    }

    /**
     * Get all permissions
     *
     * @return Collection
     */
    public function getAllPermissions(): Collection
    {
        return Permission::all();
    }

    /**
     * Get role by ID
     *
     * @param int $id
     * @return Role|null
     */
    public function findRole($id): ?Role
    {
        return Role::findOrFail($id);
    }

    /**
     * Get role permissions
     *
     * @param int $roleId
     * @return Collection
     */
    public function getRolePermissions($roleId): Collection
    {
        return Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
            ->where("role_has_permissions.role_id", $roleId)
            ->get();
    }

    /**
     * Get role permission IDs
     *
     * @param int $roleId
     * @return array
     */
    public function getRolePermissionIds($roleId): array
    {
        return DB::table("role_has_permissions")
            ->where("role_has_permissions.role_id", $roleId)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();
    }

    /**
     * Create a new role
     *
     * @param array $roleData
     * @param array|null $permissionIds
     * @return Role
     */
    public function createRole($roleData, $permissionIds = null): Role
    {
        $role = Role::create($roleData);

        $this->syncRolePermissions($role, $permissionIds);

        return $role;
    }

    /**
     * Update an existing role
     *
     * @param Role $role
     * @param array $roleData
     * @param array|null $permissionIds
     * @return Role
     */
    public function updateRole(Role $role, $roleData, $permissionIds = null): Role
    {
        $role->update($roleData);

        $this->syncRolePermissions($role, $permissionIds);

        return $role;
    }

    /**
     * Delete a role
     *
     * @param int $roleId
     * @return bool
     */
    public function deleteRole($roleId): bool
    {
        return DB::table("roles")->where('id', $roleId)->delete();
    }

    /**
     * Sync role permissions
     *
     * @param Role $role
     * @param array|null $permissionIds
     * @return void
     */
    private function syncRolePermissions(Role $role, $permissionIds = null): void
    {
        if (!empty($permissionIds)) {
            // Fetch permission names by IDs
            $permissionNames = Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();
            $role->syncPermissions($permissionNames);
        } else {
            // If no permissions are provided, remove all permissions from the role
            $role->syncPermissions([]);
        }
    }
}
