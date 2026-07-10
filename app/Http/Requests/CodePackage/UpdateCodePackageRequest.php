<?php

namespace App\Http\Requests\CodePackage;

use App\Http\Requests\CodePackage\Concerns\NormalizesPackageItems;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCodePackageRequest extends FormRequest
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
            'expires_at' => ['required', 'date'],
        ], $this->packageModeRules());
    }

    public function messages(): array
    {
        return [
            'package_items.required' => trans('main_trans.Package_items_required'),
            'package_items.min' => trans('main_trans.At_least_one_subject_unit_required'),
            'subject_ids.required' => trans('main_trans.At_least_one_subject_required'),
            'subject_ids.min' => trans('main_trans.At_least_one_subject_required'),
            'include_with_course' => trans('main_trans.At_least_one_content_type_required'),
        ];
    }
}
