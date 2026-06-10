<?php

namespace App\Http\Requests\QuestionType;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionTypeRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:255|unique:question_types,name',
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
            'name.required' => 'The question type name is required.',
            'name.string' => 'The question type name must be a string.',
            'name.max' => 'The question type name may not be greater than 255 characters.',
            'name.unique' => 'The question type name has already been taken.',
            'type.required' => 'The question type is required.',
            'type.in' => 'The question type must be either Automation or NotAutomation or TrueOrFalse.',
        ];
    }
}
