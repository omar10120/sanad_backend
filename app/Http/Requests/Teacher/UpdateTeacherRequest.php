<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:teachers,id',
            'name' => 'required|string|max:255',
            'estimation_time' => [
                'nullable'
                
              
            ],
            'whatsapp_link' => 'nullable|string|max:500',
            'instagram_link' => 'nullable|string|max:500',
            'telegram_link' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'price' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
            'subject_videos' => 'nullable|array',
            'subject_videos.*' => 'integer|exists:subjects_video,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:1048',
        ];
    }
}
