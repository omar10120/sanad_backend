<?php

namespace App\Http\Requests\YoutubeLinkVideo;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreYoutubeLinkVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'youtube_link' => ['required', 'string', 'max:500', 'url'],
            'video_time' => [
                'nullable',
                'regex:/^\d{2}:\d{2}:\d{2}$/'
              
            ],
            'lesson_video_id' => ['required', 'integer', 'exists:lessons_video,id'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => filter_var($this->input('is_active'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'name.required' => trans('main_trans.Youtube_link_video_name_required'),
            'name.string' => trans('main_trans.Youtube_link_video_name_must_be_string'),
            'name.max' => trans('main_trans.Youtube_link_video_name_max_length'),
            'youtube_link.required' => trans('main_trans.Youtube_link_required'),
            'youtube_link.string' => trans('main_trans.Youtube_link_must_be_string'),
            'youtube_link.max' => trans('main_trans.Youtube_link_max_length'),
            'youtube_link.url' => trans('main_trans.Youtube_link_must_be_url'),
            'video_time.regex' => trans('main_trans.Video_time_must_be_time'),
            'lesson_video_id.required' => trans('main_trans.Lesson_video_id_required'),
            'lesson_video_id.integer' => trans('main_trans.Lesson_video_id_must_be_integer'),
            'lesson_video_id.exists' => trans('main_trans.Lesson_video_not_exists'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => trans('main_trans.Youtube_link_video_name'),
            'youtube_link' => trans('main_trans.Youtube_link'),
            'video_time' => trans('main_trans.Video_time'),
            'lesson_video_id' => trans('main_trans.Lesson'),
        ];
    }
}
