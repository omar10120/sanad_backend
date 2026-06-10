<?php

namespace App\Http\Requests\Tag;

use Illuminate\Foundation\Http\FormRequest;

class RestoreTagRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:tags,id',
            'name' => 'required|string|max:255',
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
            'id.required' => trans('main_trans.Tag_id_is_required'),
            'id.integer' => trans('main_trans.Tag_id_must_be_integer'),
            'id.exists' => trans('main_trans.Tag_not_found'),
            'name.required' => trans('main_trans.Tag_name_is_required'),
            'name.string' => trans('main_trans.Tag_name_must_be_string'),
            'name.max' => trans('main_trans.Tag_name_max_length'),
        ];
    }
} 