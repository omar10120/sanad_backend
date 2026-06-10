<?php

namespace App\Http\Requests\Type;

use Illuminate\Foundation\Http\FormRequest;

class RestoreTypeRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:types,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'id.required' => 'Type ID is required.',
            'id.integer' => 'Type ID must be an integer.',
            'id.exists' => 'Type not found.',
        ];
    }
} 