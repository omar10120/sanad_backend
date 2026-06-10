<?php

namespace App\Services;

use App\Models\Type;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Exception;

class TypeService
{
    /**
     * Get all types
     */
    public function getAllTypes(): Collection
    {
        return Type::orderBy('order')->get();
    }

    /**
     * Get types based on user access
     */
    public function getTypesForUser(): Collection
    {
        $user = Auth::user();
        
        // Owner role has access to all types
        if ($user->hasRole('Owner')) {
            return Type::orderBy('order')->get();
        }
        
        // For other users, get only types that have subjects assigned to the user
        $userSubjectIds = $user->getAllowedSubjectIds();
        
        return Type::whereHas('subjects', function($query) use ($userSubjectIds) {
            $query->whereIn('subjects.id', $userSubjectIds);
        })->orderBy('order')->get();
    }

    /**
     * Get active types only
     */
    public function getActiveTypes(): Collection
    {
        return Type::active()->orderBy('order')->get();
    }

    /**
     * Create a new type
     */
    public function createType(array $data): Type
    {
        return Type::create($data);
    }

    /**
     * Find type by ID
     */
    public function findType(int $id): ?Type
    {
        return Type::findOrFail($id);
    }

    /**
     * Update type
     */
    public function updateType(int $id, array $data): bool
    {
        $type = Type::findOrFail($id);
        if (!$type) {
            return false;
        }

        return $type->update($data);
    }

    /**
     * Delete type with validation
     */
    public function deleteType(int $id): array
    {
        $type = Type::findOrFail($id);
        
        if (!$type->canBeDeleted()) {
            return [
                'success' => false,
                'message' => trans('main_trans.Type_has_related_data'),
            ];
        }

        try {
            $type->delete();
            return [
                'success' => true,
                'message' => trans('main_trans.Type_delete_successfully')
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Toggle type active status
     */
    public function toggleTypeStatus(Type $type): bool
    {
        return $type->update(['is_active' => !$type->is_active]);
    }

    /**
     * Get type with its subjects
     */
    public function getTypeWithSubjects(int $typeId): ?Type
    {
        return Type::with('subjects')->findOrFail($typeId);
    }

    /**
     * Get type with its subjects filtered by user access
     */
    public function getTypeWithSubjectsForUser(int $typeId): ?Type
    {
        $user = Auth::user();
        
        // Owner role has access to all types and subjects
        if ($user->hasRole('Owner')) {
            return Type::with('subjects')->findOrFail($typeId);
        }
        
        // For other users, get type with only subjects they have access to
        $userSubjectIds = $user->getAllowedSubjectIds();
        
        return Type::with(['subjects' => function($query) use ($userSubjectIds) {
            $query->whereIn('subjects.id', $userSubjectIds);
        }])->findOrFail($typeId);
    }
}
