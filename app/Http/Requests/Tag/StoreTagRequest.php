<?php

namespace App\Http\Requests\Tag;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTagRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'is_exam' => ['nullable', 'boolean'],
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
            'name.required' => trans('main_trans.Tag_name_required'),
            'name.string' => trans('main_trans.Tag_name_must_be_string'),
            'name.max' => trans('main_trans.Tag_name_max_length'),
            'subject_id.required' => trans('main_trans.Subject_required'),
            'subject_id.integer' => trans('main_trans.Subject_must_be_integer'),
            'subject_id.exists' => trans('main_trans.Subject_not_exists'),
            'is_exam.boolean' => trans('main_trans.Is_exam_must_be_boolean'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'name' => trans('main_trans.Tag_name'),
            'subject_id' => trans('main_trans.Subject'),
            'is_exam' => trans('main_trans.Is_exam'),
        ];
    }
} 