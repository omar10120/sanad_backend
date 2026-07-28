<?php

namespace App\Http\Requests\SubjectVideo;

use Illuminate\Foundation\Http\FormRequest;

class RestoreSubjectVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:subjects_video,id',
            'type_id' => 'nullable|integer|exists:types,id',
        ];
    }
}
