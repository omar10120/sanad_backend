<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ForceDeleteUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => trans('main_trans.User_id_required'),
            'id.exists' => trans('main_trans.User_not_exists'),
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => trans('main_trans.User_id'),
        ];
    }
}
