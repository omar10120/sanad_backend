<?php

namespace App\Http\Requests\Tag;

use Illuminate\Foundation\Http\FormRequest;

class ReorderTagRequest extends FormRequest
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
            'ordered_ids.*' => 'exists:tags,id'
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
            'ordered_ids.required' => 'The ordered IDs are required.',
            'ordered_ids.array' => 'The ordered IDs must be an array.',
            'ordered_ids.*.exists' => 'One or more tags do not exist.',
        ];
    }
}
