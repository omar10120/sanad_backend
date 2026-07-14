<?php

namespace App\Http\Requests\Unit;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:units,id'],
            'name' => ['required', 'string', 'max:255'],
            'teacher_id' => ['required', 'integer', 'exists:teachers,id'],
            'is_active' => ['required', 'boolean'],
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
            'id.required' => trans('main_trans.Unit_id_required'),
            'id.integer' => trans('main_trans.Unit_id_must_be_integer'),
            'id.exists' => trans('main_trans.Unit_not_exists'),
            'name.required' => trans('main_trans.Unit_name_required'),
            'name.string' => trans('main_trans.Unit_name_must_be_string'),
            'name.max' => trans('main_trans.Unit_name_max_length'),
            'teacher_id.required' => trans('main_trans.Teacher_required'),
            'teacher_id.integer' => trans('main_trans.Teacher_must_be_integer'),
            'teacher_id.exists' => trans('main_trans.Teacher_not_exists'),
            'is_active.required' => trans('main_trans.Status'),
            'is_active.boolean' => trans('main_trans.Status'),
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => trans('main_trans.Unit_id'),
            'name' => trans('main_trans.Unit_name'),
            'teacher_id' => trans('main_trans.Teacher'),
            'is_active' => trans('main_trans.Status'),
        ];
    }
}
