<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'estimation_time' => 'nullable|integer|min:0',
            'whatsapp_link' => 'nullable|string|max:500',
            'instagram_link' => 'nullable|string|max:500',
            'telegram_link' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'is_active' => 'boolean:true,false',
            'price' => 'nullable|string',
            'description' => 'nullable|string',
            'subject_video_id' => 'required|integer|exists:subjects_video,id',
            'subject_videos' => 'nullable|array',
            'subject_videos.*' => 'integer|exists:subjects_video,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}
