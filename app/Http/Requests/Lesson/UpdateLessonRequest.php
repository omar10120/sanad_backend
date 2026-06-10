<?php

namespace App\Http\Requests\Lesson;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLessonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:lessons,id',
            'title' => 'required|string|max:255',
            'subject_id' => 'required|integer|exists:subjects,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'id.required' => 'The lesson ID is required.',
            'id.integer' => 'The lesson ID must be an integer.',
            'id.exists' => 'The selected lesson does not exist.',
            'title.required' => 'The lesson title is required.',
            'title.string' => 'The lesson title must be a string.',
            'title.max' => 'The lesson title may not be greater than 255 characters.',
            'subject_id.required' => 'The subject is required.',
            'subject_id.integer' => 'The subject ID must be an integer.',
            'subject_id.exists' => 'The selected subject does not exist.',
        ];
    }
} 