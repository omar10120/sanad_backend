<?php

namespace App\Http\Requests\Student;

use App\Models\Student;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:255'],
            'father_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                'unique:students,email',
            ],
            'phone' => ['required', 'string', 'min:10', 'max:10', 'unique:students,phone'],
            'country_code' => ['required', 'string', 'min:1', 'max:10', 'unique:students,country_code'],
            'city' => ['required', 'string', 'max:255'],
            'school' => ['nullable', 'string', 'max:255'],
            'type_id' => ['required', 'integer', 'exists:types,id'],
            'status' => ['nullable', 'boolean'],
            'max_devices' => ['nullable', 'integer', 'min:1', 'max:10'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
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
            'first_name.required' => trans('main_trans.First_name_required'),
            'first_name.string' => trans('main_trans.First_name_must_be_string'),
            'first_name.max' => trans('main_trans.First_name_max_length'),
            'father_name.required' => trans('main_trans.Father_name_required'),
            'father_name.string' => trans('main_trans.Father_name_must_be_string'),
            'father_name.max' => trans('main_trans.Father_name_max_length'),
            'last_name.required' => trans('main_trans.Last_name_required'),
            'last_name.string' => trans('main_trans.Last_name_must_be_string'),
            'last_name.max' => trans('main_trans.Last_name_max_length'),
            'email.nullable' => trans('main_trans.Email_nullable'),
            'email.email' => trans('main_trans.Email_invalid'),
            'email.unique' => trans('main_trans.Email_exists'),
            'phone.required' => trans('main_trans.Phone_required'),
            'phone.string' => trans('main_trans.Phone_must_be_string'),
            'phone.min' => trans('main_trans.Phone_min_length'),
            'phone.max' => trans('main_trans.Phone_max_length'),
            'phone.unique' => trans('main_trans.Phone_exists'),
            'country_code.required' => trans('main_trans.Country_code_required'),
            'country_code.string' => trans('main_trans.Country_code_must_be_string'),
            'country_code.max' => trans('main_trans.Country_code_max_length'),
            'country_code.min' => trans('main_trans.Country_code_min_length'),
            'country_code.unique' => trans('main_trans.Country_code_exists'),
            'city.required' => trans('main_trans.City_required'),
            'city.string' => trans('main_trans.City_must_be_string'),
            'city.max' => trans('main_trans.City_max_length'),
            'school.string' => trans('main_trans.School_must_be_string'),
            'school.max' => trans('main_trans.School_max_length'),
            'type_id.required' => trans('main_trans.Type_required'),
            'type_id.integer' => trans('main_trans.Type_must_be_integer'),
            'type_id.exists' => trans('main_trans.Type_not_exists'),
            'status.boolean' => trans('main_trans.Status_must_be_boolean'),
            'max_devices.integer' => trans('main_trans.Max_devices_must_be_integer'),
            'max_devices.min' => trans('main_trans.Max_devices_min'),
            'max_devices.max' => trans('main_trans.Max_devices_max'),
            'password.required' => trans('main_trans.Password_required'),
            'password.min' => trans('main_trans.Password_min'),
            'password.confirmed' => trans('main_trans.Password_not_match'),
            'password_confirmation.required' => trans('main_trans.Password_confirmation_required'),
            'photo.image' => trans('main_trans.Photo_must_be_image'),
            'photo.mimes' => trans('main_trans.Photo_must_be_jpeg_png_jpg'),
            'photo.max' => trans('main_trans.Photo_max_size'),
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
            'first_name' => trans('main_trans.First_name'),
            'father_name' => trans('main_trans.Father_name'),
            'last_name' => trans('main_trans.Last_name'),
            'email' => trans('main_trans.Email'),
            'phone' => trans('main_trans.Phone'),
            'country_code' => trans('main_trans.Country_code'),
            'city' => trans('main_trans.City'),
            'school' => trans('main_trans.School'),
            'type_id' => trans('main_trans.Type'),
            'status' => trans('main_trans.Status'),
            'max_devices' => trans('main_trans.Max_devices'),
            'password' => trans('main_trans.Password'),
            'password_confirmation' => trans('main_trans.Password_confirmation'),
            'photo' => trans('main_trans.Photo'),
        ];
    }
} 