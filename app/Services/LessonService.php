<?php

namespace App\Services;

use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Question;
use App\Models\QuestionGroup;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

class LessonService
{
    /**
     * Get all lessons ordered by subject
     */
    public function getAllLessons(): Collection
    {
        return Lesson::orderBy('subject_id')->orderBy('order')->get();
    }

    /**
     * Get all subjects
     */
    public function getAllSubjects(): Collection
    {
        return Subject::all();
    }

    /**
     * Create a new lesson
     */
    public function createLesson(array $data): Lesson
    {
        return Lesson::create($data);
    }

    /**
     * Update a lesson
     */
    public function updateLesson(int $id, array $data): bool
    {
        $lesson = Lesson::findOrFail($id);
        return $lesson->update($data);
    }

    /**
     * Delete a lesson with validation
     */
    public function deleteLesson(int $id): array
    {
        $lesson = Lesson::findOrFail($id);
        
        if (!$lesson->canBeDeleted()) {
            return [
                'success' => false,
                'message' => trans('main_trans.Lesson_has_related_question_groups'),
            ];
        }

        try {
            $lesson->delete();
            return [
                'success' => true,
                'message' => trans('main_trans.Lesson_delete_successfully')
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Toggle lesson active status
     */
    public function toggleLesson(Lesson $lesson): bool
    {
        return $lesson->update(['is_active' => !$lesson->is_active]);
    }

    /**
     * Get lesson by ID
     */
    public function findLesson(int $id): ?Lesson
    {
        return Lesson::findOrFail($id);
    }

    /**
     * Get lesson with question groups
     */
    public function getLessonWithQuestionGroups(int $lessonId): ?Lesson
    {
        return Lesson::with('questionGroups')->find($lessonId);
    }

    /**
     * Get archived lessons for a subject
     */
    public function getArchivedLessons(int $subjectId): Collection
    {
        return Lesson::where('subject_id', $subjectId)->onlyTrashed()->get();
    }

    /**
     * Restore an archived lesson
     */
    public function restoreLesson(int $id): bool
    {
        $lesson = Lesson::withTrashed()->findOrFail($id);
        return $lesson->restore();
    }

    /**
     * Permanently delete an archived lesson
     */
    public function forceDeleteLesson(int $id): bool
    {
        $lesson = Lesson::withTrashed()->findOrFail($id);
        return $lesson->forceDelete();
    }

    /**
     * Get all questions for a lesson
     */
    public function getLessonQuestions(int $lessonId): Collection
    {
        $lesson = Lesson::findOrFail($lessonId);
        return $lesson->getAllQuestions();
    }

    /**
     * Get questions by type for a lesson
     */
    public function getLessonQuestionsByType(int $lessonId, int $typeId): Collection
    {
        return Question::join('question_groups', 'questions.question_group_id', '=', 'question_groups.id')
            ->where('question_groups.lesson_id', $lessonId)
            ->where('questions.type_id', $typeId)
            ->select('questions.*')
            ->get();
    }

    /**
     * Convert delta to plain text
     */
    public function deltaToPlainText($delta): string
    {
        if (is_string($delta)) {
            $delta = json_decode($delta, true);
        }

        if (empty($delta['ops'])) {
            return '';
        }

        $plainText = '';
        foreach ($delta['ops'] as $op) {
            if (isset($op['insert']) && is_string($op['insert'])) {
                $plainText .= $op['insert'];
            }
        }

        return $plainText;
    }

    /**
     * Get all active lessons
     */
    public function getActiveLessons(): Collection
    {
        return Lesson::active()->get();
    }

    /**
     * Get lessons by subject
     */
    public function getLessonsBySubject(int $subjectId): Collection
    {
        return Lesson::where('subject_id', $subjectId)->get();
    }
} 