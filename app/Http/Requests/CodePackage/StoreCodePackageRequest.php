<?php

namespace App\Http\Requests\CodePackage;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCodePackageRequest extends FormRequest
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
            'expires_at' => ['required', 'date', 'after:today'],
            'subject_ids' => ['required', 'array', 'min:1'],
            'subject_ids.*' => ['required', 'integer', 'exists:subjects,id'],
            'codes_count' => ['required', 'integer', 'min:1', 'max:10000'],
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
            'name.required' => trans('main_trans.Package_name_required'),
            'name.string' => trans('main_trans.Package_name_must_be_string'),
            'name.max' => trans('main_trans.Package_name_max_length'),
            'expires_at.required' => trans('main_trans.Expiry_date_required'),
            'expires_at.date' => trans('main_trans.Expiry_date_must_be_valid_date'),
            'expires_at.after' => trans('main_trans.Expiry_date_must_be_future'),
            'subject_ids.required' => trans('main_trans.Subjects_required'),
            'subject_ids.array' => trans('main_trans.Subjects_must_be_array'),
            'subject_ids.min' => trans('main_trans.At_least_one_subject_required'),
            'subject_ids.*.required' => trans('main_trans.Subject_id_required'),
            'subject_ids.*.integer' => trans('main_trans.Subject_id_must_be_integer'),
            'subject_ids.*.exists' => trans('main_trans.Subject_not_exists'),
            'codes_count.required' => trans('main_trans.Codes_count_required'),
            'codes_count.integer' => trans('main_trans.Codes_count_must_be_integer'),
            'codes_count.min' => trans('main_trans.Codes_count_min_one'),
            'codes_count.max' => trans('main_trans.Codes_count_max_limit'),
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
            'name' => trans('main_trans.Package_name'),
            'expires_at' => trans('main_trans.Expiry_date'),
            'subject_ids' => trans('main_trans.Subjects'),
            'codes_count' => trans('main_trans.Codes_count'),
        ];
    }
} 