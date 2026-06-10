<?php

namespace App\Http\Requests\QuestionType;

use Illuminate\Foundation\Http\FormRequest;

class DeleteQuestionTypeRequest extends FormRequest
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
            'id' => 'required|integer|exists:question_types,id',
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
            'id.required' => 'The question type ID is required.',
            'id.integer' => 'The question type ID must be an integer.',
            'id.exists' => 'The selected question type does not exist.',
        ];
    }
} 