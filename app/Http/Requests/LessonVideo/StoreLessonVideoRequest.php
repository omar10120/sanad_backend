<?php

namespace App\Http\Requests\LessonVideo;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreLessonVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'unit_id' => ['required', 'integer', 'exists:units,id'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function prepareForValidation(): void
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
            'title.required' => trans('main_trans.Lesson_video_title_required'),
            'title.string' => trans('main_trans.Lesson_video_title_must_be_string'),
            'title.max' => trans('main_trans.Lesson_video_title_max_length'),   
            'unit_id.required' => trans('main_trans.Unit_required'),
            'unit_id.integer' => trans('main_trans.Unit_id_must_be_integer'),
            'unit_id.exists' => trans('main_trans.Unit_not_exists'),
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => trans('main_trans.Lesson_video_title'),
            'unit_id' => trans('main_trans.Unit'),
        ];
    }
}
