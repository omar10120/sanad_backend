<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Student;

/**
 * @method Student user()
 * @method array all()
 * @method void replace(array $input)
 */
class ChangePhoneRequest extends FormRequest
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
        $studentId = $this->user()->id;

        return [
            'new_phone' => 'required|string|size:10|regex:/^09/|unique:students,phone,' . $studentId . ',country_code,' . $this->user()->country_code,
            'verification_code' => 'required|string|size:6',
            'country_code' => 'required|string',
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
            'new_phone.required' => 'New phone number is required.',
            'new_phone.size' => 'New phone number must be exactly 10 digits.',
            'new_phone.regex' => 'New phone number must start with 09.',
            'new_phone.unique' => 'This phone number is already registered for this country.',
            'verification_code.required' => 'Verification code is required.',
            'verification_code.size' => 'Verification code must be exactly 6 digits.',
            'country_code.required' => 'Country code is required.',
        ];
    }
}
