<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;

class UserService
{
    /**
     * Get all users
     *
     * @return Collection
     */
    public function getAllUsers(): Collection
    {
        return User::all();
    }

    /**
     * Get all roles for user creation/editing
     *
     * @return array
     */
    public function getAllRoles(): array
    {
        return Role::pluck('name', 'name')->all();
    }

    /**
     * Get user by ID
     *
     * @param int $id
     * @return User|null
     */
    public function findUser($id): ?User
    {
        return User::findOrFail($id);
    }

    /**
     * Get user roles
     *
     * @param User $user
     * @return array
     */
    public function getUserRoles(User $user)
    {
        return $user->roles->pluck('name', 'name')->all();
    }

    /**
     * Handle file upload
     *
     * @param Request $request
     * @param string $fieldName
     * @return string|null
     */
    public function handleFileUpload(Request $request, $fieldName = 'photo')
    {
        if ($request->hasFile($fieldName)) {
            return $request->file($fieldName)->getClientOriginalName();
        }

        return null;
    }

    /**
     * Create user
     *
     * @param array $userData
     * @param string|null $photoName
     * @param string $roleName
     * @return User
     */
    public function createUser($userData, $photoName = null, $roleName)
    {
        $user = User::create($userData);

        if ($photoName) {
            $user->update(['photo' => $photoName]);
        }

        $role = Role::findByName($roleName);
        $user->assignRole([$role->id]);

        return $user;
    }

    /**
     * Move uploaded file
     *
     * @param Request $request
     * @param int $userId
     * @param string $photoName
     * @return void
     */
    public function moveUploadedFile(Request $request, $userId, $photoName)
    {
        if ($request->hasFile('photo')) {
            $request->photo->move(public_path('assets/image/Users/' . $userId), $photoName);
        }
    }

    /**
     * Update user photo
     *
     * @param Request $request
     * @param User $user
     * @return string|null
     */
    public function updateUserPhoto(Request $request, User $user)
    {
        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $photoName = $image->getClientOriginalName();

            // Delete old photo if exists
            $this->deleteUserPhoto($user->id, $user->photo);

            // Update user photo
            $user->update(['photo' => $photoName]);

            // Move new photo
            $request->photo->move(public_path('assets/image/Users/' . $user->id), $photoName);

            return $photoName;
        }

        return null;
    }

    /**
     * Update user data
     *
     * @param User $user
     * @param array $data
     * @return void
     */
    public function updateUser(User $user, $data)
    {
        $user->update($data);
    }

    /**
     * Update user role
     *
     * @param User $user
     * @param string $roleName
     * @return void
     */
    public function updateUserRole(User $user, $roleName)
    {
        $role = Role::findByName($roleName);
        $user->syncRoles([$role->id]);
    }

    /**
     * Update user password
     *
     * @param User $user
     * @param string $password
     * @return void
     */
    public function updateUserPassword(User $user, $password)
    {
        $user->update([
            'password' => Hash::make($password),
        ]);
    }

    /**
     * Delete user photo
     *
     * @param int $userId
     * @param string|null $photoName
     * @return void
     */
    public function deleteUserPhoto($userId, $photoName)
    {
        if ($photoName) {
            $path = 'assets/image/Users/' . $userId . '/' . $photoName;
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }

    /**
     * Delete user
     *
     * @param int $userId
     * @return bool
     */
    public function deleteUser($userId)
    {
        $user = User::find($userId);

        if ($user) {
            // Delete user photo
            $this->deleteUserPhoto($user->id, $user->photo);

            // Delete user
            return $user->delete();
        }

        return false;
    }

    /**
     * Get soft-deleted users
     */
    public function getArchivedUsers(): Collection
    {
        return User::onlyTrashed()->get();
    }

    /**
     * Restore a soft-deleted user
     */
    public function restoreUser(int $userId): bool
    {
        $user = User::onlyTrashed()->findOrFail($userId);

        return (bool) $user->restore();
    }

    /**
     * Permanently delete a soft-deleted user
     */
    public function forceDeleteUser(int $userId): bool
    {
        $user = User::onlyTrashed()->findOrFail($userId);

        $this->deleteUserPhoto($user->id, $user->photo);

        return (bool) $user->forceDelete();
    }

    /**
     * Prepare user data for creation
     *
     * @param Request $request
     * @return array
     */
    public function prepareUserData(Request $request)
    {
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['show_all_teachers'] = $request->boolean('show_all_teachers');

        return $input;
    }

    /**
     * Prepare user data for update
     *
     * @param Request $request
     * @return array
     */
    public function prepareUserUpdateData(Request $request)
    {
        $data = [
            'phone' => $request->phone,
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'email' => $request->email,
            'status' => $request->status,
        ];

        if ($request->has('show_all_teachers')) {
            $data['show_all_teachers'] = $request->boolean('show_all_teachers');
        }

        return $data;
    }
}
