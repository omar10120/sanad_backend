<?php

namespace App\Services;

use App\Models\Question;
use Illuminate\Support\Facades\Storage;

class ArchivedQuestionService
{
    /**
     * Restore a deleted question
     */
    public function restoreQuestion($question_id)
    {
        $question = Question::withTrashed()
            ->with('questionGroup')
            ->where('id', $question_id)
            ->first();

        if (!$question) {
            throw new \Exception('Question not found');
        }

        // Check if the question group exists and is not deleted
        $questionGroup = $question->questionGroup;
        if (!$questionGroup || $questionGroup->trashed()) {
            throw new \Exception('Question group not found or has been deleted');
        }

        $question->restore();
        
        // Reorder all questions in the group after restoration
        $this->reorderQuestionsInGroup($questionGroup);
        
        return $question;
    }

    /**
     * Reorder questions in a group
     */
    private function reorderQuestionsInGroup($question_group): void
    {
        if (!$question_group) {
            throw new \Exception('Question group is required for reordering');
        }

        $questions = $question_group->questions()
            ->orderBy('order')
            ->get();
        
        $index = 1;
        foreach ($questions as $question) {
            $question->order = $index++;
            $question->save();
        }
    }

    /**
     * Permanently delete a question
     */
    public function forceDeleteQuestion($question_id)
    {
        $question = Question::withTrashed()->where('id', $question_id)->first();

        if (!$question) {
            throw new \Exception('Question not found');
        }

        // Delete question photo if exists
        if ($question->question_photo) {
            $path = 'assets/image/Question/' . $question_id . '/question-photo/' . $question->question_photo;
            if (file_exists($path)) {
                unlink($path);
            }
        }

        // Delete hint photo if exists
        if ($question->hint_photo) {
            $path_hint = 'assets/image/Question/' . $question_id . '/hint-photo/' . $question->hint_photo;
            if (file_exists($path_hint)) {
                unlink($path_hint);
            }
        }

        $question->forceDelete();
        return true;
    }

    /**
     * Get all archived questions
     */
    public function getAllArchivedQuestions()
    {
        return Question::onlyTrashed()->get();
    }

    /**
     * Get archived questions by lesson
     */
    public function getArchivedQuestionsByLesson($lesson_id)
    {
        return Question::onlyTrashed()
            ->whereHas('questionGroup', function ($query) use ($lesson_id) {
                $query->where('lesson_id', $lesson_id);
            })
            ->get();
    }

    /**
     * Get archived questions by question group
     */
    public function getArchivedQuestionsByQuestionGroup($question_group_id)
    {
        return Question::onlyTrashed()
            ->where('question_group_id', $question_group_id)
            ->with(['typeQuestion', 'questionGroup'])
            ->get();
    }

    /**
     * Get question group
     */
    public function getQuestionGroup($question_group_id)
    {
        $questionGroup = \App\Models\QuestionGroup::find($question_group_id);
        
        if (!$questionGroup) {
            throw new \Exception('Question group not found');
        }
        
        return $questionGroup;
    }

    /**
     * Get lesson
     */
    public function getLesson($lesson_id)
    {
        $lesson = \App\Models\Lesson::find($lesson_id);
        
        if (!$lesson) {
            throw new \Exception('Lesson not found');
        }
        
        return $lesson;
    }
}
