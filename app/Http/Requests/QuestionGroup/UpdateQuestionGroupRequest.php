<?php

namespace App\Http\Requests\QuestionGroup;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionGroupRequest extends FormRequest
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
            'lesson_id' => 'sometimes|integer|exists:lessons,id',
            'name' => 'sometimes|string|max:255',
            'order' => 'sometimes|integer|min:1',
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
            'lesson_id.integer' => 'The lesson ID must be an integer.',
            'lesson_id.exists' => 'The selected lesson does not exist.',
            'name.string' => 'The question group name must be a string.',
            'name.max' => 'The question group name may not be greater than 255 characters.',
            'order.integer' => 'The order must be an integer.',
            'order.min' => 'The order must be at least 1.',
        ];
    }
} 