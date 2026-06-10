<?php

namespace App\Http\Requests\QuestionGroup;

use Illuminate\Foundation\Http\FormRequest;

class DeleteQuestionGroupRequest extends FormRequest
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
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'id.required' => 'The question group ID is required.',
            'id.integer' => 'The question group ID must be an integer.',
            'id.exists' => 'The selected question group does not exist.',
        ];
    }
} 