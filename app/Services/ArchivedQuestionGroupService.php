<?php

namespace App\Services;

use App\Models\QuestionGroup;
use App\Models\Lesson;
use Illuminate\Support\Facades\DB;

class ArchivedQuestionGroupService
{
    /**
     * Get all archived question groups
     */
    public function getArchivedQuestionGroups($lesson_id = null)
    {
        $query = QuestionGroup::onlyTrashed()->with(['lesson', 'questions']);

        if ($lesson_id) {
            $query->where('lesson_id', $lesson_id);
        }

        return $query->get();
    }

    /**
     * Get lesson by ID
     */
    public function getLesson($lesson_id)
    {
        return Lesson::find($lesson_id);
    }

    /**
     * Restore a deleted question group
     */
    public function restoreQuestionGroup($question_group_id)
    {
        $questionGroup = QuestionGroup::withTrashed()->where('id', $question_group_id)->first();

        if (!$questionGroup) {
            throw new \Exception('Question group not found');
        }

        $lesson = $questionGroup->lesson;
        
        // Get the highest order in the lesson
        $maxOrder = $lesson->questionGroups()->max('order') ?? 0;
        
        $questionGroup->restore();
        $questionGroup->order = $maxOrder + 1;
        $questionGroup->save();
        
        // Reorder all question groups in the lesson after restoration
        $this->reorderQuestionGroups($lesson);

        return $questionGroup;
    }

    /**
     * Permanently delete a question group
     */
    public function forceDeleteQuestionGroup($question_group_id)
    {
        $questionGroup = QuestionGroup::withTrashed()->where('id', $question_group_id)->first();

        if (!$questionGroup) {
            throw new \Exception('Question group not found');
        }

        return DB::transaction(function () use ($questionGroup) {
            // Delete all questions in the group
            foreach ($questionGroup->questions as $question) {
                // Delete question photo if exists
                if ($question->question_photo) {
                    $path = 'assets/image/Question/' . $question->id . '/question-photo/' . $question->question_photo;
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }

                // Delete hint photo if exists
                if ($question->hint_photo) {
                    $path_hint = 'assets/image/Question/' . $question->id . '/hint-photo/' . $question->hint_photo;
                    if (file_exists($path_hint)) {
                        unlink($path_hint);
                    }
                }

                $question->forceDelete();
            }

            // Permanently delete the question group
            $questionGroup->forceDelete();

            // Reorder remaining question groups in the lesson
            $this->reorderQuestionGroups($questionGroup->lesson);

            return true;
        });
    }

    /**
     * Reorder question groups in a lesson after deletion
     */
    private function reorderQuestionGroups($lesson)
    {
        $questionGroups = $lesson->questionGroups()
            ->orderBy('order')
            ->get();

        $index = 1;
        foreach ($questionGroups as $questionGroup) {
            $questionGroup->order = $index++;
            $questionGroup->save();
        }
    }
} 