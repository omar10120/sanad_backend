<?php

namespace App\Http\Resources;

use App\Models\QuestionType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class LessonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $questionTypes = QuestionType::join('questions', 'question_types.id', '=', 'questions.type_id')
            ->join('question_groups', 'questions.question_group_id', '=', 'question_groups.id')
            ->where('question_groups.lesson_id', $this->id)
            ->select('question_types.id', 'question_types.name', 'question_types.type')
            ->distinct()
            ->get()
            ->map(function($type) {
                return [
                    'id' => $type->id,
                    'name' => $type->name,
                    'type' => $type->type
                ];
            });

        return [
            'id' => $this->id,
            'display_order' => $this->order,
            'title' => $this->title,
            'subject_id' => $this->subject_id,
            'question_types' => $questionTypes,
        ];
    }
}
