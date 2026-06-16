<?php

namespace App\Http\Requests\SubjectVideo;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubjectVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:subjects_video,id',
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'light_color_code' => 'nullable|string|max:7',
            'dark_color_code' => 'nullable|string|max:7',
            'icon_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean',
            'types' => 'nullable|array',
            'types.*' => 'integer|exists:types,id',
        ];
    }
}
