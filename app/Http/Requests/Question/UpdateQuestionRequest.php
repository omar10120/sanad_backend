<?php

namespace App\Http\Requests\Question;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateQuestionRequest extends FormRequest
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
            'id' => 'required|exists:questions,id',
            'subject_id' => 'required|exists:subjects,id',
            'lesson_id' => 'required|exists:lessons,id',
            'type_id' => 'required|exists:question_types,id',
            'text_question' => 'required|string',
            'choices' => [
                'required_if:type_id,1',
                Rule::when(request()->type_id == 1, 'string'),
            ],
            'correctAnswer' => [
                'required_if:type_id,1',
                Rule::when(request()->type_id == 1, 'integer|min:1'),
            ],
            'hint' => 'nullable|string',
            'question_group_id' => 'required|integer',
            'question_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'hint_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'is_edited' => 'nullable|boolean',
            'clear_hint' => 'sometimes|boolean',
            'clear_tags' => 'sometimes|boolean',
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
            'id.required' => 'معرف السؤال مطلوب',
            'id.exists' => 'السؤال المحدد غير موجود',
            'lesson_id.required' => 'معرف الدرس مطلوب',
            'lesson_id.exists' => 'الدرس المحدد غير موجود',
            'type_id.required' => 'نوع السؤال مطلوب',
            'type_id.exists' => 'نوع السؤال المحدد غير موجود',
            'text_question.required' => 'نص السؤال مطلوب',
            'text_question.string' => 'نص السؤال يجب أن يكون نص',
            'choices.required_if' => 'الخيارات مطلوبة لهذا النوع من الأسئلة',
            'choices.string' => 'الخيارات يجب أن تكون نص',
            'correctAnswer.required' => 'الإجابة الصحيحة مطلوبة',
            'correctAnswer.integer' => 'الإجابة الصحيحة يجب أن تكون رقم',
            'correctAnswer.min' => 'الإجابة الصحيحة يجب أن تكون أكبر من صفر',
            'hint.string' => 'التلميح يجب أن يكون نص',
            'question_group_id.required' => 'معرف مجموعة الأسئلة مطلوب',
            'question_group_id.integer' => 'معرف مجموعة الأسئلة يجب أن يكون رقم',
            'question_photo.image' => 'صورة السؤال يجب أن تكون صورة',
            'question_photo.mimes' => 'صورة السؤال يجب أن تكون من نوع: jpeg, png, jpg, gif',
            'question_photo.max' => 'صورة السؤال يجب أن لا تتجاوز 2 ميجابايت',
            'hint_photo.image' => 'صورة التلميح يجب أن تكون صورة',
            'hint_photo.mimes' => 'صورة التلميح يجب أن تكون من نوع: jpeg, png, jpg, gif',
            'hint_photo.max' => 'صورة التلميح يجب أن لا تتجاوز 2 ميجابايت',
            'tags.array' => 'العلامات يجب أن تكون مصفوفة',
            'tags.*.exists' => 'العلامة المحددة غير موجودة',
            'is_edited.boolean' => 'حقل التعديل يجب أن يكون صحيح أو خطأ',
        ];
    }
}
