<?php

namespace App\Http\Requests\Auth;

use App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * @method Student user()
 * @method array all()
 * @method void replace(array $input)
 */
class ProfileUpdateRequest extends FormRequest
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
        $studentId = $this->user()->id;

        return [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'father_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|unique:students,email,' . $studentId,
            'phone' => 'sometimes|string|unique:students,phone,' . $studentId,
            'country_code' => 'sometimes|string|unique:students,country_code,' . $studentId,
            'city' => 'sometimes|in:damascus,damascus_suburb,homs,hama,aleppo,idlib,tartus,latakia,deir_ezzor,hasaka,raqqa,sweida,daraa,quneitra',
            'school' => 'sometimes|string|max:255|nullable',
            'password' => 'sometimes|min:6|confirmed',
            'certificate_id' => 'sometimes|exists:types,id',
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
            'first_name.string' => 'الاسم الأول يجب أن يكون نص',
            'first_name.max' => 'الاسم الأول يجب أن يكون أقل من 255 حرف',
            'last_name.string' => 'اسم العائلة يجب أن يكون نص',
            'last_name.max' => 'اسم العائلة يجب أن يكون أقل من 255 حرف',
            'father_name.string' => 'اسم الأب يجب أن يكون نص',
            'father_name.max' => 'اسم الأب يجب أن يكون أقل من 255 حرف',
            'email.email' => 'يرجى إدخال بريد إلكتروني صالح',
            'email.unique' => 'هذا البريد الإلكتروني مستخدم مسبقاً',
            'phone.unique' => 'رقم الهاتف مستخدم مسبقاً',
            'country_code.string' => 'رمز الدولة يجب أن يكون نص',
            'city.in' => 'المدينة المحددة غير صالحة',
            'school.max' => 'اسم المدرسة يجب أن يكون أقل من 255 حرف',
            'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
            'password_confirmation.required_with' => 'تأكيد كلمة المرور مطلوب عند تغيير كلمة المرور',
            'certificate_id.exists' => 'الرقم القياسي غير موجود',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Normalize possible camelCase payloads to snake_case and clean empty strings
        $input = $this->all();

        // Map camelCase to snake_case keys for mobile/web clients
        $keyMap = [
            'firstName' => 'first_name',
            'lastName' => 'last_name',
            'fatherName' => 'father_name',
            'typeId' => 'type_id',
            'deviceId' => 'device_id',
            'passwordConfirmation' => 'password_confirmation',
        ];

        foreach ($keyMap as $from => $to) {
            if (array_key_exists($from, $input) && !array_key_exists($to, $input)) {
                $input[$to] = $input[$from];
            }
        }

        // Remove empty strings and convert them to null for optional fields
        foreach (['school', 'password'] as $field) {
            if (isset($input[$field]) && $input[$field] === '') {
                $input[$field] = null;
            }
        }

        $this->replace($input);
    }
}
