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
        return [
            'name' => ['required', 'string', 'max:255'],
            'expires_at' => ['required', 'date'],
            'include_with_course' => ['sometimes', 'boolean'],
            'include_without_course' => ['sometimes', 'boolean'],
            'package_items' => ['nullable', 'array'],
            'subject_ids' => ['nullable', 'array'],
        ];
    }
}
