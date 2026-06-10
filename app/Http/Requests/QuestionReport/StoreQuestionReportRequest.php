<?php

namespace App\Http\Requests\QuestionReport;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionReportRequest extends FormRequest
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
            'report_type' => 'required|in:spelling_error,scientific_error',
            'description' => 'required|string|max:2000',
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
            'report_type.required' => 'نوع التقرير مطلوب',
            'report_type.in' => 'نوع التقرير يجب أن يكون خطأ إملائي أو خطأ علمي',
            'description.required' => 'وصف التقرير مطلوب',
            'description.string' => 'وصف التقرير يجب أن يكون نص',
            'description.max' => 'وصف التقرير يجب أن لا يتجاوز 2000 حرف',
        ];
    }
}
