<?php

namespace App\Http\Requests\SubjectVideo;

use Illuminate\Foundation\Http\FormRequest;

class DeleteSubjectVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:subjects_video,id',
        ];
    }
}
