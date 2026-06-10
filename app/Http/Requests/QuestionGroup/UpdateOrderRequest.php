<?php

namespace App\Http\Requests\QuestionGroup;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
            'ordered_groups' => 'required|array',
            'ordered_groups.*' => 'exists:question_groups,id',
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
            'ordered_groups.required' => 'The ordered groups are required.',
            'ordered_groups.array' => 'The ordered groups must be an array.',
            'ordered_groups.*.exists' => 'One or more question groups do not exist.',
        ];
    }
} 