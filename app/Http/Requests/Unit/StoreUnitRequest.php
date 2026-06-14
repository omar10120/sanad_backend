<?php

namespace App\Http\Requests\Unit;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'teacher_id' => ['required', 'integer', 'exists:teachers,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => trans('main_trans.Unit_name_required'),
            'name.string' => trans('main_trans.Unit_name_must_be_string'),
            'name.max' => trans('main_trans.Unit_name_max_length'),
            'teacher_id.required' => trans('main_trans.Teacher_required'),
            'teacher_id.integer' => trans('main_trans.Teacher_must_be_integer'),
            'teacher_id.exists' => trans('main_trans.Teacher_not_exists'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => trans('main_trans.Unit_name'),
            'teacher_id' => trans('main_trans.Teacher'),
        ];
    }
}
