<?php

namespace App\Http\Requests\CodePackage;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCodePackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'expires_at' => ['required', 'date'],
            'package_items' => ['required', 'array', 'min:1'],
            'package_items.*.subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'package_items.*.unit_id' => ['required', 'integer', 'exists:units,id'],
        ];
    }
}
