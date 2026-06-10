<?php

namespace App\Http\Requests\AppUpdate;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAppUpdateRequest extends FormRequest
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
            'version' => ['sometimes', 'required', 'string', 'max:50'],
            'platform' => ['sometimes', 'required', 'string', 'in:android,ios'],
            'changelog' => ['nullable', 'string', 'max:2000'],
            'is_force_update' => ['nullable', 'boolean'],
            'update_url' => ['nullable', 'url', 'max:500'],
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
            'version.required' => trans('main_trans.Version_required'),
            'version.string' => trans('main_trans.Version_must_be_string'),
            'version.max' => trans('main_trans.Version_max_length'),
            'platform.required' => trans('main_trans.Platform_required'),
            'platform.string' => trans('main_trans.Platform_must_be_string'),
            'platform.in' => trans('main_trans.Platform_must_be_android_or_ios'),
            'changelog.string' => trans('main_trans.Changelog_must_be_string'),
            'changelog.max' => trans('main_trans.Changelog_max_length'),
            'is_force_update.boolean' => trans('main_trans.Is_force_update_must_be_boolean'),
            'update_url.url' => trans('main_trans.Update_url_must_be_valid_url'),
            'update_url.max' => trans('main_trans.Update_url_max_length'),
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
            'version' => trans('main_trans.Version'),
            'platform' => trans('main_trans.Platform'),
            'changelog' => trans('main_trans.Changelog'),
            'is_force_update' => trans('main_trans.Is_force_update'),
            'update_url' => trans('main_trans.Update_url'),
        ];
    }
} 