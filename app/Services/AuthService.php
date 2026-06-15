<?php

namespace App\Services;

use App\Http\Resources\AuthResource;
use App\Models\Device;
use App\Models\PhoneVerificationCode;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Register a new student
     * @param array $data
     * @return array
     */
    public function registerStudent(array $data): array
    {
        $phoneVerification = app(PhoneVerificationService::class)->verifyCode(
            $data['phone'],
            $data['verification_code'],
            PhoneVerificationCode::TYPE_REGISTRATION,
            true
        );

        if (!$phoneVerification['success']) {
            return [
                'student' => null,
                'token' => null,
                'message' => $phoneVerification['message'],
                'status' => 400
            ];
        }

        $student = Student::create([
            'first_name' => $data['first_name'],
            'father_name' => $data['father_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'],
            'city' => $data['city'],
            'email' => $data['email'] ?? null,
            'school' => $data['school'] ?? null,
            'type_id' => $data['type_id'] ?? null,
            'password' => Hash::make($data['password']),
            'phone_verified_at' => now(),
        ]);

        // استخدام الدالة المحسّنة
        $device = $this->createOrUpdateDevice($data);

        $student->attachDevice($device, true);

        $token = $student->createToken($student->first_name.'-AuthToken')->plainTextToken;

        $student = new AuthResource(Student::findOrFail($student->id));

        return [
            'student' => $student,
            'token' => 'Bearer ' . $token,
            'message' => 'تم إنشاء الطالب بنجاح',
            'status' => 201
        ];
    }

    /**
     * Login a student
     * @param array $data
     * @return array
     */
    public function loginStudent(array $data): array
    {
        $student = Student::where('phone', $data['phone'])->first();

        if (!$student || !Hash::check($data['password'], $student->password)) {
            return [
                'success' => false,
                'message' => 'بيانات الدخول غير صحيحة، يرجى التأكد منها والمحاولة مرة أخرى أو قم بإنشاء حساب جديد',
                'status' => 404
            ];
        }

        if ($student->status == 0) {
            return [
                'success' => false,
                'message' => trans('auth.inactive_account'),
                'status' => 403
            ];
        }

        if (!$student->hasVerifiedPhone()) {
            return [
                'success' => false,
                'message' => 'يرجى التحقق من رقم الهاتف أولاً',
                'status' => 403
            ];
        }

        $device = $this->createOrUpdateDevice($data);

        $existingStudentDevice = $student->studentDevices()
            ->where('device_id', $device->id)
            ->first();

        if ($existingStudentDevice) {
            $existingStudentDevice->setAsCurrent();
            $existingStudentDevice->updateLastLogin();
        } else {
            if (!$student->canAddDevice()) {
                return [
                    'success' => false,
                    'message' => 'هذا الحساب مسجل على جهاز آخر مسبقاً.',
                    'status' => 403
                ];
            }

            $student->attachDevice($device, true);
        }

        $student->tokens()->delete();
        $token = $student->createToken($student->first_name . '-AuthToken')->plainTextToken;

        return [
            'success' => true,
            'student' => new AuthResource($student),
            'token' => 'Bearer ' . $token,
            'message' => 'تم تسجيل الدخول بنجاح',
            'status' => 200
        ];
    }

    /**
     * إنشاء أو تحديث معلومات الجهاز (مع تحديث FCM token)
     *
     * @param array $data
     * @return Device
     */
    private function createOrUpdateDevice(array $data): Device
    {
        $deviceData = [
            'brand' => $data['brand'] ?? null,
            'model' => $data['model'] ?? null,
            'device' => $data['device'] ?? null,
            'manufacturer' => $data['manufacturer'] ?? null,
            'product' => $data['product'] ?? null,
            'name' => $data['device_name'] ?? null,
            'os_name' => $data['os_name'] ?? 'Unknown',
            'os_version' => $data['os_version'] ?? null,
            'is_active' => true,
            'last_active_at' => now()
        ];

        if (isset($data['fcm_token']) && !empty($data['fcm_token'])) {
            $deviceData['fcm_token'] = $data['fcm_token'];
        }

        $device = Device::updateOrCreate(
            ['device_id' => $data['device_id']], // شرط البحث
            $deviceData  // البيانات للإنشاء أو التحديث
        );

        return $device;
    }

    /**
     * Logout a student
     *
     * @param Student $student
     * @return array
     */
    public function logoutStudent(Student $student): array
    {
        $student->tokens()->delete();

        return [
            'message' => 'تم تسجيل الخروج بنجاح',
            'status' => 200
        ];
    }

    /**
     * Get student profile
     *
     * @param Student $student
     * @return array
     */
    public function getStudentProfile(Student $student): array
    {
        $data = new AuthResource($student);

        return [
            'data' => $data,
            'message' => 'ملف الطالب',
            'status' => 200
        ];
    }

    /**
     * Update student profile
     *
     * @param Student $student
     * @param array $data
     * @return array
     */
    public function updateStudentProfile(Student $student, array $data): array
    {
        $updated = false;

        if (isset($data['email']) && $data['email'] !== $student->email) {
            $student->email = $data['email'];
            $updated = true;
        }

        if (isset($data['password']) && !empty($data['password'])) {
            $student->password = Hash::make($data['password']);
            $updated = true;
        }

        if (isset($data['first_name'])) {
            $student->first_name = $data['first_name'];
            $updated = true;
        }

        if (isset($data['last_name'])) {
            $student->last_name = $data['last_name'];
            $updated = true;
        }

        if (isset($data['certificate_id'])) {
            $student->type_id = $data['certificate_id'];
            $updated = true;
        }

        if (isset($data['father_name'])) {
            $student->father_name = $data['father_name'];
            $updated = true;
        }

        if (isset($data['phone'])) {
            $student->phone = $data['phone'];
            $updated = true;
        }

        if (isset($data['city'])) {
            $student->city = $data['city'];
            $updated = true;
        }

        if (isset($data['school'])) {
            $student->school = $data['school'];
            $updated = true;
        }

        if ($updated) {
            $student->save();
        }

        return [
            'message' => 'تم التحديث بنجاح',
            'student' => new AuthResource($student),
            'status' => 200
        ];
    }

    /**
     * Send phone change verification code
     *
     * @param Student $student
     * @param string $newPhone
     * @return array
     */
    public function sendPhoneChangeVerificationCode(Student $student, string $newPhone): array
    {
        if ($student->phone === $newPhone) {
            return [
                'success' => false,
                'message' => 'يجب أن يكون رقم الهاتف الجديد مختلفاً عن الرقم الحالي',
                'status' => 400
            ];
        }

        $existingStudent = Student::where('phone', $newPhone)->where('id', '!=', $student->id)->first();
        if ($existingStudent) {
            return [
                'success' => false,
                'message' => 'رقم الهاتف هذا مسجل مسبقاً',
                'status' => 400
            ];
        }

        $result = app(PhoneVerificationService::class)->sendVerificationCode($newPhone, PhoneVerificationCode::TYPE_PHONE_CHANGE);

        return [
            'success' => $result['success'],
            'message' => $result['message'],
            'status' => $result['success'] ? 200 : 400
        ];
    }

    /**
     * Change phone number
     *
     * @param Student $student
     * @param array $data
     * @return array
     */
    public function changePhoneNumber(Student $student, array $data): array
    {
        $newPhone = $data['new_phone'];
        $verificationCode = $data['verification_code'];

        if ($student->phone === $newPhone) {
            return [
                'success' => false,
                'message' => 'يجب أن يكون رقم الهاتف الجديد مختلفاً عن الرقم الحالي',
                'status' => 400
            ];
        }

        $phoneVerification = app(PhoneVerificationService::class)->verifyCode(
            $newPhone,
            $verificationCode,
            PhoneVerificationCode::TYPE_PHONE_CHANGE,
            true
        );

        if (!$phoneVerification['success']) {
            return [
                'success' => false,
                'message' => $phoneVerification['message'],
                'status' => 400
            ];
        }

        // Check if new phone is still available (double check)
        $existingStudent = Student::where('phone', $newPhone)->where('id', '!=', $student->id)->first();
        if ($existingStudent) {
            return [
                'success' => false,
                'message' => 'رقم الهاتف هذا مسجل مسبقاً',
                'status' => 400
            ];
        }

        $student->update([
            'phone' => $newPhone,
            'phone_verified_at' => now(),
        ]);

        return [
            'success' => true,
            'student' => new AuthResource($student->fresh()),
            'message' => 'تم تغيير رقم الهاتف بنجاح',
            'status' => 200
        ];
    }
}
