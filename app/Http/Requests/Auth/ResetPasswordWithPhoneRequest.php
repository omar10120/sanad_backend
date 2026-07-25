<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordWithPhoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => 'required|string|min:7|max:14',
            'verification_code' => 'required|string|size:6',
            'password' => 'required|string|min:8',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Phone number is required.',
            'phone.min' => 'Phone number must be at least 7 digits.',
            'phone.max' => 'Phone number must be at most 14 digits.',
            'verification_code.required' => 'Verification code is required.',
            'verification_code.size' => 'Verification code must be exactly 6 digits.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }
}
