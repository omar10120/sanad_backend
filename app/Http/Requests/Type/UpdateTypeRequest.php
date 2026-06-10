<?php

namespace App\Http\Requests\Type;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTypeRequest extends FormRequest
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
            'id' => 'required|integer|exists:types,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('types', 'name')->ignore($this->id),
            ],
            'is_active' => 'boolean',
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
            'id.required' => 'The type ID is required.',
            'id.integer' => 'The type ID must be an integer.',
            'id.exists' => 'The selected type does not exist.',
            'name.required' => 'The type name is required.',
            'name.string' => 'The type name must be a string.',
            'name.max' => 'The type name may not be greater than 255 characters.',
            'name.unique' => 'The type name has already been taken.',
            'is_active.boolean' => 'The active status must be true or false.',
        ];
    }
} 