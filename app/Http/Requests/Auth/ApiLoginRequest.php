<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class ApiLoginRequest extends FormRequest
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
            'phone' => 'required|string|size:10|regex:/^09/',
            'country_code' => 'required|string|size:4|regex:/^\+/',
            'password' => 'required|string|min:8',
            'device_id' => 'nullable|string',
            'brand' => 'nullable|string',
            'model' => 'nullable|string',
            'device' => 'nullable|string',
            'manufacturer' => 'nullable|string',
            'product' => 'nullable|string',
            'device_name' => 'nullable|string',
            'os_name' => 'nullable|string',
            'os_version' => 'nullable|string',
            'fcm_token' => 'nullable|string',
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
            'phone.required' => 'Phone number is required.',
            'phone.size' => 'Phone number must be exactly 10 digits.',
            'phone.regex' => 'Phone number must start with 09.',
            'country_code.required' => 'Country code is required.',
            'country_code.size' => 'Country code must be exactly 3 characters long.',
            'country_code.regex' => 'Country code must begin with +.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'device_id.required' => 'Device ID is required.',
            'brand.required' => 'Brand is required.',
            'model.required' => 'Model is required.',
            'device.required' => 'Device is required.',
            'manufacturer.required' => 'Manufacturer is required.',
            'product.required' => 'Product is required.',
            'device_name.required' => 'Devive Name is required.',
            'os_name.required' => 'OS Name is required.',
            'os_version.required' => 'OS Version is required.',
            'fcm_token.required' => 'FCM Token is required.',
        ];
    }
}
