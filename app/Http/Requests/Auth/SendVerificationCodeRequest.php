<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\PhoneVerificationCode;

class SendVerificationCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => 'required|string|min:7|max:14',
            'country_code' => 'required|string',
            'type' => 'required|in:registration,password_reset,phone_change',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Phone number is required.',
            'phone.min' => 'Phone number must be at least 7 digits.',
            'phone.max' => 'Phone number must be at most 14 digits.',
            'country_code.required' => 'Country code is required.',
            'type.required' => 'Verification type is required.',
            'type.in' => 'Invalid verification type.',
        ];
    }
}