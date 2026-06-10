<?php

namespace App\Http\Requests\QuestionGroup;

use Illuminate\Foundation\Http\FormRequest;

class ReorderQuestionGroupRequest extends FormRequest
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
            'ordered_ids' => 'required|array',
            'ordered_ids.*' => 'exists:question_groups,id'
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ordered_ids.required' => 'The ordered IDs are required.',
            'ordered_ids.array' => 'The ordered IDs must be an array.',
            'ordered_ids.*.exists' => 'One or more question group IDs do not exist.',
        ];
    }
}

