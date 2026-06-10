<?php

namespace App\Http\Requests\QuestionGroup;

use Illuminate\Foundation\Http\FormRequest;

class RestoreQuestionGroupRequest extends FormRequest
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
            'id' => 'required|integer|exists:question_groups,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'id.required' => 'Question group ID is required.',
            'id.integer' => 'Question group ID must be an integer.',
            'id.exists' => 'Question group not found.',
        ];
    }
} 