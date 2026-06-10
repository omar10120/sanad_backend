<?php

namespace App\Services;

use App\Models\Lesson;
use App\Models\Question;
use App\Models\QuestionGroup;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

class QuestionGroupService
{
    /**
     * Get all question groups for a lesson ordered by order
     */
    public function getQuestionGroupsByLesson(Lesson $lesson): Collection
    {
        return $lesson->questionGroups()
            ->orderBy('order')
            ->get();
    }

    /**
     * Get a question group by ID with its questions
     */
    public function getQuestionGroupWithQuestions(int $groupId): ?QuestionGroup
    {
        return QuestionGroup::with('questions')->find($groupId);
    }

    /**
     * Create a new question group
     */
    public function createQuestionGroup(array $data): QuestionGroup
    {
        return QuestionGroup::create($data);
    }

    /**
     * Update a question group
     */
    public function updateQuestionGroup(int $id, array $data): bool
    {
        $questionGroup = QuestionGroup::findOrFail($id);
        return $questionGroup->update($data);
    }

    /**
     * Delete a question group (soft delete) with validation
     */
    public function deleteQuestionGroup(int $id): array
    {
        $questionGroup = QuestionGroup::findOrFail($id);
        
        // if (!$questionGroup->canBeDeleted()) {
        //     return [
        //         'success' => false,
        //         'message' => trans('main_trans.QuestionGroup_has_related_questions'),
        //     ];
        // }

        try {
            $lesson = $questionGroup->lesson;
            $questionGroup->delete(); // This will soft delete if the model uses SoftDeletes
            $this->orderQuestionGroup($lesson);
            return [
                'success' => true,
                'message' => trans('main_trans.Question_group_delete_successfully')
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Reorder questions group in a lesson
     */
    private function orderQuestionGroup($lesson): void
    {
        $question_groups = $lesson->questionGroups()
            ->orderBy('order')
            ->get();
        $index = 1;
        foreach ($question_groups as $question_group){
            $question_group->order = $index++;
            $question_group->save();
        }
    }

    /**
     * Update the order of question groups
     */
    public function updateGroupsOrder(Lesson $lesson, array $orderedGroups): bool
    {
        $lesson->updateGroupsOrder($orderedGroups);
        return true;
    }

    /**
     * Move a question group to a specific position
     */
    public function moveGroupToPosition(Lesson $lesson, int $groupId, int $newPosition): bool
    {
        $group = $lesson->questionGroups()->findOrFail($groupId);
        $currentPosition = $group->order;

        // Validate new position
        $maxPosition = $lesson->questionGroups()->count();
        if ($newPosition > $maxPosition) {
            $newPosition = $maxPosition;
        }
        if ($newPosition < 1) {
            $newPosition = 1;
        }

        return DB::transaction(function () use ($lesson, $group, $currentPosition, $newPosition) {
            if ($newPosition < $currentPosition) {
                // Move up - increment orders of groups between new and current position
                $lesson->questionGroups()
                    ->where('order', '>=', $newPosition)
                    ->where('order', '<', $currentPosition)
                    ->increment('order');
            } else {
                // Move down - decrement orders of groups between current and new position
                $lesson->questionGroups()
                    ->where('order', '>', $currentPosition)
                    ->where('order', '<=', $newPosition)
                    ->decrement('order');
            }

            return $group->update(['order' => $newPosition]);
        });
    }

    /**
     * Convert delta format to plain text
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
     * Process questions text for display
     */
    public function processQuestionsForDisplay(QuestionGroup $questionGroup): QuestionGroup
    {
        $questionGroup->questions->transform(function ($question) {
            $delta = $question->text_question;
            $question->questionText = $this->deltaToPlainText($delta);

            if (strlen($question->questionText) > 80) {
                $question->questionText = substr($question->questionText, 0, 80);
                $lastSpacePos = strrpos($question->questionText, ' ');
                $question->questionText = substr($question->questionText, 0, $lastSpacePos) . '...';
            }

            return $question;
        });

        return $questionGroup;
    }

    /**
     * Get question group with processed questions for display
     */
    public function getQuestionGroupForDisplay(int $groupId): ?QuestionGroup
    {
        $questionGroup = $this->getQuestionGroupWithQuestions($groupId);

        if ($questionGroup) {
            return $this->processQuestionsForDisplay($questionGroup);
        }

        return null;
    }
}
