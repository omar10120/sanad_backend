<?php

namespace App\Http\Requests\CodePackage;

use App\Http\Requests\CodePackage\Concerns\NormalizesPackageItems;
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'codes_count' => ['required', 'integer', 'min:1', 'max:10000'],
            'expires_at' => ['required', 'date', 'after:today'],
            'include_with_course' => ['sometimes', 'boolean'],
            'include_without_course' => ['sometimes', 'boolean'],
            'package_items' => ['nullable', 'array'],
            'subject_ids' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => trans('main_trans.Package_name_required'),
            'codes_count.required' => trans('main_trans.Codes_count_required'),
            'codes_count.integer' => trans('main_trans.Codes_count_must_be_integer'),
            'codes_count.min' => trans('main_trans.Codes_count_min_limit'),
            'codes_count.max' => trans('main_trans.Codes_count_max_limit'),
            'expires_at.required' => trans('main_trans.Expiry_date_required'),
            'expires_at.after' => trans('main_trans.Expiry_date_must_be_future'),
        ];
    }
}
