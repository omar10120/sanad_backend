<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyPhoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => 'required|string|min:7|max:14',
            'code' => 'required|string|size:6',
            'type' => 'required|in:registration,password_reset,phone_change',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Phone number is required.',
            'phone.min' => 'Phone number must be at least 7 digits.',
            'phone.max' => 'Phone number must be at most 14 digits.',
            'code.required' => 'Verification code is required.',
            'code.size' => 'Verification code must be exactly 6 digits.',
            'type.required' => 'Verification type is required.',
            'type.in' => 'Invalid verification type.',
        ];
    }
}