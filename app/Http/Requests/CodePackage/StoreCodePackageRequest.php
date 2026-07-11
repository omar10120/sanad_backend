<?php

namespace App\Http\Requests\CodePackage;

use App\Http\Requests\CodePackage\Concerns\NormalizesPackageItems;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCodePackageRequest extends FormRequest
{
    use NormalizesPackageItems;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge([
            'name' => ['required', 'string', 'max:255'],
            'expires_at' => ['required', 'date', 'after:today'],
            'codes_count' => ['required', 'integer', 'min:1', 'max:10000'],
        ], $this->packageModeRules());
    }

    public function messages(): array
    {
        return [
            'name.required' => trans('main_trans.Package_name_required'),
            'expires_at.required' => trans('main_trans.Expiry_date_required'),
            'expires_at.after' => trans('main_trans.Expiry_date_must_be_future'),
            // 'package_items.nullable' => trans('main_trans.Package_items_required'),
            // 'package_items.min' => trans('main_trans.At_least_one_subject_unit_required'),
            // 'subject_ids.nullable' => trans('main_trans.At_least_one_subject_required'),
            // 'subject_ids.min' => trans('main_trans.At_least_one_subject_required'),
            'include_with_course.nullable' => trans('main_trans.At_least_one_content_type_required'),
            'codes_count.nullable' => trans('main_trans.Codes_count_required'),
            // 'codes_count.min' => trans('main_trans.Codes_count_min_one'),
            'codes_count.max' => trans('main_trans.Codes_count_max_limit'),
        ];
    }
}
