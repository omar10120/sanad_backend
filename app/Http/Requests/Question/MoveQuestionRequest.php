<?php

namespace App\Http\Requests\Question;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class MoveQuestionRequest extends FormRequest
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
            'question_id' => 'required|exists:questions,id',
            'new_position' => 'required|integer|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'question_id.required' => 'معرف السؤال مطلوب',
            'question_id.exists' => 'السؤال المحدد غير موجود',
            'new_position.required' => 'الموضع الجديد مطلوب',
            'new_position.integer' => 'الموضع الجديد يجب أن يكون رقم',
            'new_position.min' => 'الموضع الجديد يجب أن يكون أكبر من صفر',
        ];
    }
} 