<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
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
            'id' => ['required', 'integer', 'exists:users,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
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
            'id.required' => trans('main_trans.User_id_required'),
            'id.exists' => trans('main_trans.User_not_exists'),
            'password.required' => trans('main_trans.Password_required'),
            'password.min' => trans('main_trans.Password_min'),
            'password.confirmed' => trans('main_trans.Password_not_match'),
            'password_confirmation.required' => trans('main_trans.Password_confirmation_required'),
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
            'id' => trans('main_trans.User_id'),
            'password' => trans('main_trans.Password'),
            'password_confirmation' => trans('main_trans.Password_confirmation'),
        ];
    }
}
