<?php

namespace App\Http\Requests\QuestionType;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateQuestionTypeRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $questionTypeId = $this->route('type') ? $this->route('type')->id : $this->input('id');

        return [
            'id' => 'required|integer|exists:question_types,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('question_types', 'name')->ignore($questionTypeId),
            ],
            'type' => 'required|in:Automation,NotAutomation,TrueOrFalse',
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
            'name.required' => 'The question type name is required.',
            'name.string' => 'The question type name must be a string.',
            'name.max' => 'The question type name may not be greater than 255 characters.',
            'name.unique' => 'The question type name has already been taken.',
            'type.required' => 'The question type is required.',
            'type.in' => 'The question type must be either Automation or NotAutomation or TrueOrFalse.',
        ];
    }
}
