<?php

namespace App\Http\Requests\AppUpdate;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CheckAppUpdateRequest extends FormRequest
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
            'platform' => ['required', 'string', 'in:android,ios'],
            'version' => ['required', 'string', 'max:50'],
            'device_date' => ['required', 'date'],
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
            'platform.required' => trans('main_trans.Platform_required'),
            'platform.string' => trans('main_trans.Platform_must_be_string'),
            'platform.in' => trans('main_trans.Platform_must_be_android_or_ios'),
            'version.required' => trans('main_trans.Version_required'),
            'version.string' => trans('main_trans.Version_must_be_string'),
            'version.max' => trans('main_trans.Version_max_length'),
            'device_date.required' => trans('main_trans.Device_date_required'),
            'device_date.date' => trans('main_trans.Device_date_must_be_valid_date'),
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
            'platform' => trans('main_trans.Platform'),
            'version' => trans('main_trans.Version'),
            'device_date' => trans('main_trans.Device_date'),
        ];
    }
} 