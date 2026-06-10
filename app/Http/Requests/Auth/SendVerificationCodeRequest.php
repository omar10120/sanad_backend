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
            'phone' => 'required|string|size:10|regex:/^09/',
            'type' => 'required|in:registration,password_reset,phone_change',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Phone number is required.',
            'phone.size' => 'Phone number must be exactly 10 digits.',
            'phone.regex' => 'Phone number must start with 09.',
            'type.required' => 'Verification type is required.',
            'type.in' => 'Invalid verification type.',
        ];
    }
}