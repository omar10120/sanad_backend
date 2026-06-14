<?php

namespace App\Http\Requests\Unit;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DeleteUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:units,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => trans('main_trans.Unit_id_required'),
            'id.integer' => trans('main_trans.Unit_id_must_be_integer'),
            'id.exists' => trans('main_trans.Unit_not_exists'),
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => trans('main_trans.Unit_id'),
        ];
    }
}
