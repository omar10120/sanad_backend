<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('user') ?? $this->route('id');

        return [
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'phone' => ['nullable', 'string', 'max:14', 'min:7'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'is_instructor' => ['nullable', 'boolean'],
            'status' => ['nullable', 'boolean'],
            'roles_name' => ['required', 'array', 'min:1'],
            'roles_name.*' => ['required', 'string', 'exists:roles,name'],
            'subjects' => ['nullable', 'array'],
            'subjects.*' => ['integer', 'exists:subjects,id'],
            'teacher_id' => ['nullable', 'integer', 'exists:teachers,id'],
            'show_all_teachers' => ['nullable', 'boolean'],
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
            'name_ar.required' => trans('main_trans.Name_ar_required'),
            'name_en.required' => trans('main_trans.Name_en_required'),
            'email.nullable' => trans('main_trans.Email_required'),
            'email.email' => trans('main_trans.Email_invalid'),
            'email.unique' => trans('main_trans.Email_exists'),
            'roles_name.required' => trans('main_trans.Role_required'),
            'roles_name.*.exists' => trans('main_trans.Role_not_exists'),
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
            'name_ar' => trans('main_trans.Name_ar'),
            'name_en' => trans('main_trans.Name_en'),
            'email' => trans('main_trans.Email'),
            'phone' => trans('main_trans.Phone'),
            'photo' => trans('main_trans.Photo'),
            'bio' => trans('main_trans.Bio'),
            'is_instructor' => trans('main_trans.Is_instructor'),
            'status' => trans('main_trans.Status'),
            'roles_name' => trans('main_trans.Roles'),
            
        ];
    }
}
