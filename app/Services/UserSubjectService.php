<?php

namespace App\Services;

use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class UserSubjectService
{
    /**
     * Assign subjects to a user, optionally linking the same teacher_id on each pivot row.
     */
    public function assignSubjectsToUser(int $userId, array $subjectIds, ?int $teacherId = null): bool
    {
        $user = User::findOrFail($userId);

        $subjectIds = collect($subjectIds)
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $syncData = [];
        foreach ($subjectIds as $subjectId) {
            $syncData[$subjectId] = [
                'teacher_id' => $teacherId,
            ];
        }

        DB::transaction(function () use ($user, $syncData) {
            $user->subjects()->sync($syncData);
        });

        return true;
    }

    /**
     * Remove subjects from a user
     */
    public function removeSubjectsFromUser(int $userId, array $subjectIds): bool
    {
        $user = User::findOrFail($userId);
        $user->subjects()->detach($subjectIds);

        return true;
    }

    /**
     * Get all subjects for a specific user
     */
    public function getUserSubjects(int $userId): Collection
    {
        $user = User::findOrFail($userId);

        return $user->subjects()->get();
    }

    /**
     * Get all users for a specific subject
     */
    public function getSubjectUsers(int $subjectId): Collection
    {
        $subject = Subject::findOrFail($subjectId);

        return $subject->users()->get();
    }

    /**
     * Check if a user has access to a specific subject
     */
    public function userHasAccessToSubject(int $userId, int $subjectId, ?int $unitId = null): bool
    {
        $user = User::findOrFail($userId);

        if ($user->hasRole('Owner')) {
            return true;
        }

        return $user->hasAccessToSubject($subjectId, $unitId);
    }

    /**
     * Get all subjects (for owner role or when assigning subjects)
     */
    public function getAllSubjects(): Collection
    {
        return Subject::orderBy('name')->get();
    }

    /**
     * Get all teachers
     */
    public function getAllTeachers(): Collection
    {
        return Teacher::orderBy('name')->get();
    }

    /**
     * Get subjects that a user doesn't have access to
     */
    public function getUnassignedSubjectsForUser(int $userId)
    {
        $user = User::findOrFail($userId);

        if ($user->hasRole('Owner')) {
            return collect();
        }

        $assignedSubjectIds = $user->getAllowedSubjectIds();

        return Subject::whereNotIn('id', $assignedSubjectIds)->get();
    }
}
