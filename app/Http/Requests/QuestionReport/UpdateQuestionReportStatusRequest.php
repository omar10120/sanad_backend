<?php

namespace App\Http\Requests\QuestionReport;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionReportStatusRequest extends FormRequest
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
            'status' => 'required|in:pending,reviewed,resolved,rejected',
            'admin_notes' => 'nullable|string|max:2000',
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
            'status.required' => 'حالة التقرير مطلوبة',
            'status.in' => 'حالة التقرير يجب أن تكون: معلق، مراجع، محلول، أو مرفوض',
            'admin_notes.string' => 'ملاحظات الإدارة يجب أن تكون نص',
            'admin_notes.max' => 'ملاحظات الإدارة يجب أن لا تتجاوز 2000 حرف',
        ];
    }
} 