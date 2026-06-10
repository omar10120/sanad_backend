<?php

namespace App\Http\Requests\QuestionGroup;

use Illuminate\Foundation\Http\FormRequest;

class MoveGroupRequest extends FormRequest
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
            'group_id' => 'required|integer|exists:question_groups,id',
            'new_position' => 'required|integer|min:1',
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
            'group_id.required' => 'The group ID is required.',
            'group_id.integer' => 'The group ID must be an integer.',
            'group_id.exists' => 'The selected question group does not exist.',
            'new_position.required' => 'The new position is required.',
            'new_position.integer' => 'The new position must be an integer.',
            'new_position.min' => 'The new position must be at least 1.',
        ];
    }
} 