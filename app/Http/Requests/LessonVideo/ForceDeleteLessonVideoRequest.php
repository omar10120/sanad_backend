<?php

namespace App\Http\Requests\LessonVideo;

use Illuminate\Foundation\Http\FormRequest;

class ForceDeleteLessonVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:lessons_video,id',
        ];
    }
}
