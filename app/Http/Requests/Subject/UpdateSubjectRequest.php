<?php

namespace App\Http\Requests\Subject;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubjectRequest extends FormRequest
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
            'id' => 'required|integer|exists:subjects,id',
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:500',
            'teacher' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'light_color_code' => 'nullable|string|max:7',
            'dark_color_code' => 'nullable|string|max:7',
            'is_active' => 'boolean',
            'types' => 'nullable|array',
            'types.*' => 'integer|exists:types,id',
            'icon_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
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
            'id.required' => 'The subject ID is required.',
            'id.integer' => 'The subject ID must be an integer.',
            'id.exists' => 'The selected subject does not exist.',
            'name.required' => 'The subject name is required.',
            'name.string' => 'The subject name must be a string.',
            'name.max' => 'The subject name may not be greater than 255 characters.',
            'icon.string' => 'The icon must be a string.',
            'icon.max' => 'The icon may not be greater than 255 characters.',
            'link.string' => 'The link must be a string.',
            'link.max' => 'The link may not be greater than 500 characters.',
            'teacher.string' => 'The teacher name must be a string.',
            'teacher.max' => 'The teacher name may not be greater than 255 characters.',
            'description.string' => 'The description must be a string.',
            'light_color_code.string' => 'The light color code must be a string.',
            'light_color_code.max' => 'The light color code may not be greater than 7 characters.',
            'dark_color_code.string' => 'The dark color code must be a string.',
            'dark_color_code.max' => 'The dark color code may not be greater than 7 characters.',
            'is_active.boolean' => 'The active status must be true or false.',
            'types.array' => 'The types must be an array.',
            'types.*.integer' => 'Each type ID must be an integer.',
            'types.*.exists' => 'The selected type does not exist.',
            'icon_photo.image' => 'The icon photo must be an image.',
            'icon_photo.mimes' => 'The icon photo must be a file of type: jpeg, png, jpg.',
            'icon_photo.max' => 'The icon photo may not be greater than 2MB.',
        ];
    }
} 