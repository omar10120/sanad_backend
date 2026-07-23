<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class RegisterRequest extends FormRequest
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
            'first_name' => 'required|string|min:2|max:255',
            'father_name' => 'required|string|min:2|max:255',
            'last_name' => 'required|string|min:2|max:255',
            'phone' => 'required|string|min:7|max:14|unique:students',
            'country_code' => 'required|string|size:4|regex:/^\+/',
            'verification_code' => 'required|string|size:6|regex:/^\d+$/',
            'city' => 'required|string|max:255',
            'email' => 'nullable|string|email|unique:students',
            'school' => 'nullable|string|min:2|max:255',
            'type_id' => 'nullable|exists:types,id',
            'password' => 'required|string|min:8',
            'device_id' => 'required|string',
            'brand' => 'required|string',
            'model' => 'required|string',
            'device' => 'required|string',
            'manufacturer' => 'required|string',
            'product' => 'required|string',
            'device_name' => 'required|string',
            'os_name' => 'required|string',
            'os_version' => 'required|string',
            'fcm_token' => 'required|string',
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
            'first_name.required' => 'First name is required.',
            'first_name.min' => 'First name must be at least 2 characters long.',
            'father_name.required' => 'Father name is required.',
            'father_name.min' => 'Father name must be at least 2 characters long.',
            'last_name.required' => 'Last name is required.',
            'last_name.min' => 'Last name must be at least 2 characters long.',
            'phone.required' => 'Phone number is required.',
            'phone.size' => 'Phone number must be exactly 10 characters long.',
            'phone.regex' => 'Phone number must begin with 09.',
            'phone.unique' => 'This phone number is already registered.',
            'country_code.required' => 'Country code is required.',
            'country_code.size' => 'Country code must be at lest 4 characters long.',
            'country_code.regex' => 'Country code must begin with +.',
            'city.required' => 'City is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'verification_code.required' => 'Verification code is required.',
            'verification_code.regex' => 'Verification code must be exactly 6 digits.',
            'verification_code.size' => 'Verification code must be exactly 6 digits.',
            'school.min' => 'School name must be at least 2 characters long.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'device_id.required' => 'Device ID is required.',
            'type_id.exists' => 'Selected type is invalid.',
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
