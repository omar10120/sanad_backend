<?php

namespace App\Services;

use App\Models\User;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Collection;

class UserSubjectService
{
    /**
     * Assign subjects to a user
     */
    public function assignSubjectsToUser(int $userId, array $subjectIds): bool
    {
        $user = User::findOrFail($userId);
        $user->subjects()->sync($subjectIds);
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
    public function userHasAccessToSubject(int $userId, int $subjectId): bool
    {
        $user = User::findOrFail($userId);

        // Owner role has access to all subjects
        if ($user->hasRole('Owner')) {
            return true;
        }

        return $user->hasAccessToSubject($subjectId);
    }

    /**
     * Get all subjects (for owner role or when assigning subjects)
     */
    public function getAllSubjects(): Collection
    {
        return Subject::all();
    }

    /**
     * Get subjects that a user doesn't have access to
     */
    public function getUnassignedSubjectsForUser(int $userId)
    {
        $user = User::findOrFail($userId);

        // If user is owner, they have access to all subjects
        if ($user->hasRole('Owner')) {
            return collect();
        }

        $assignedSubjectIds = $user->getAllowedSubjectIds();
        return Subject::whereNotIn('id', $assignedSubjectIds)
            ->get();
    }
}
