<?php

namespace App\Http\Requests\LessonVideo;

use Illuminate\Foundation\Http\FormRequest;

class ReorderLessonVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ordered_ids' => 'required|array',
            'ordered_ids.*' => 'exists:lessons_video,id',
        ];
    }
}
