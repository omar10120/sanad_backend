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
     * Assign subjects to a user (independent of teacher access).
     * Pivot teacher_id mirrors users.teacher_id when subjects exist.
     */
    public function assignSubjectsToUser(int $userId, array $subjectIds, ?int $teacherId = null): bool
    {
        $user = User::findOrFail($userId);

        $subjectIds = collect($subjectIds)
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $pivotTeacherId = $teacherId ?? ($user->show_all_teachers ? null : $user->teacher_id);

        $syncData = [];
        foreach ($subjectIds as $subjectId) {
            $syncData[$subjectId] = [
                'teacher_id' => $pivotTeacherId,
            ];
        }

        DB::transaction(function () use ($user, $syncData) {
            $user->subjects()->sync($syncData);
        });

        return true;
    }

    /**
     * Assign teacher access independently of subjects.
     */
    public function assignTeacherAccess(int $userId, bool $showAllTeachers, ?int $teacherId = null): bool
    {
        $user = User::findOrFail($userId);

        $resolvedTeacherId = $showAllTeachers ? null : $teacherId;

        DB::transaction(function () use ($user, $showAllTeachers, $resolvedTeacherId) {
            $user->update([
                'show_all_teachers' => $showAllTeachers,
                'teacher_id' => $resolvedTeacherId,
            ]);

            // Keep existing subject pivots aligned when present.
            if ($user->subjects()->exists()) {
                DB::table('user_has_subject')
                    ->where('user_id', $user->id)
                    ->update(['teacher_id' => $resolvedTeacherId]);
            }
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

    /**
     * Get assigned teacher_id from users table (falls back to pivot for legacy rows).
     */
    public function getUserTeacherId(int $userId): ?int
    {
        $user = User::findOrFail($userId);

        if ($user->teacher_id) {
            return (int) $user->teacher_id;
        }

        $teacherId = DB::table('user_has_subject')
            ->where('user_id', $userId)
            ->whereNotNull('teacher_id')
            ->value('teacher_id');

        return $teacherId ? (int) $teacherId : null;
    }

    /**
     * Restricted teacher IDs for a user.
     * null  → show all teachers (Owner or show_all_teachers)
     * []    → show no teachers
     * [ids] → show only those teachers
     */
    public function getRestrictedTeacherIdsForUser(int $userId): ?array
    {
        $user = User::findOrFail($userId);

        if ($user->hasRole('Owner') || $user->show_all_teachers) {
            return null;
        }

        if ($user->teacher_id) {
            return [(int) $user->teacher_id];
        }

        return [];
    }
}
