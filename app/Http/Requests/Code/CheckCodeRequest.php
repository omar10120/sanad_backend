<?php

namespace App\Http\Requests\Code;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CheckCodeRequest extends FormRequest
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
            'code' => ['required', 'string', 'min:1', 'max:255'],
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
            'code.required' => trans('main_trans.Code_required'),
            'code.string' => trans('main_trans.Code_must_be_string'),
            'code.min' => trans('main_trans.Code_min_length'),
            'code.max' => trans('main_trans.Code_max_length'),
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
            'code' => trans('main_trans.Code'),
        ];
    }
} 