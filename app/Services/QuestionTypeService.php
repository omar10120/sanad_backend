<?php

namespace App\Services;

use App\Models\QuestionType;
use Illuminate\Database\Eloquent\Collection;
use Exception;

class QuestionTypeService
{
    /**
     * Get all question types
     */
    public function getAllQuestionTypes(): Collection
    {
        return QuestionType::all();
    }

    /**
     * Create a new question type
     */
    public function createQuestionType(array $data): QuestionType
    {
        return QuestionType::create($data);
    }

    /**
     * Find question type by ID
     */
    public function findQuestionType(int $id): QuestionType
    {
        return QuestionType::findOrFail($id);
    }

    /**
     * Update question type
     */
    public function updateQuestionType(int $id, array $data): bool
    {
        $questionType = QuestionType::findOrFail($id);
        if (!$questionType) {
            return false;
        }

        return $questionType->update($data);
    }

    /**
     * Delete question type with validation
     */
    public function deleteQuestionType(int $id): array
    {
        $questionType = QuestionType::findOrFail($id);
        
        if (!$questionType->canBeDeleted()) {
            return [
                'success' => false,
                'message' => trans('main_trans.QuestionType_has_related_questions'),
            ];
        }

        try {
            $questionType->delete();
            return [
                'success' => true,
                'message' => trans('main_trans.Question_type_delete_successfully')
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
} 