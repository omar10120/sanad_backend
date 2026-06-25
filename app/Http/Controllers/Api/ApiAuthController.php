<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ApiLoginRequest;
use App\Http\Requests\Auth\ProfileUpdateRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\SendVerificationCodeRequest;
use App\Http\Requests\Auth\VerifyPhoneRequest;
use App\Http\Requests\Auth\ResetPasswordWithPhoneRequest;
use App\Http\Requests\Auth\SendPhoneChangeVerificationRequest;
use App\Http\Requests\Auth\ChangePhoneRequest;
use App\Models\PhoneVerificationCode;
use App\Models\Student;
use App\Services\AuthService;
use App\Services\PhoneVerificationService;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiAuthController extends Controller
{
    use ApiResponseTrait;

    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->registerStudent($request->validated());

        return $this->apiResponseLogin(
            $result['student'],
            $result['message'],
            $result['status'],
            $result['token']
        );
    }

    public function login(ApiLoginRequest $request): JsonResponse
    {
        $result = $this->authService->loginStudent($request->validated());

        if (!$result['success']) {
            return $this->apiResponse(null, $result['message'], $result['status']);
        }

        return $this->apiResponseLogin(
            $result['student'],
            $result['message'],
            $result['status'],
            $result['token']
        );
    }

    public function logout(): JsonResponse
    {
        $result = $this->authService->logoutStudent(auth('student')->user());

        return $this->apiResponse(new \stdClass(), $result['message'], $result['status']);
    }

    public function profile(Request $request): JsonResponse
    {
        $result = $this->authService->getStudentProfile($request->user());

        return $this->apiResponse($result['data'], $result['message'], $result['status']);
    }

    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->updateStudentProfile(
                $request->user(),
                $request->validated()
            );

            return $this->apiResponse(
                $result['student'],
                $result['message'],
                $result['status']
            );

        } catch (Exception $e) {
            return $this->apiResponse(
                null,
                'حدث خطأ أثناء تحديث البيانات',
                500
            );
        }
    }

    public function sendVerificationCode(SendVerificationCodeRequest $request): JsonResponse
    {
        $student = Student::where('phone', $request->validated()['phone'])->first();
        

        if($request->validated()['type'] == PhoneVerificationCode::TYPE_PASSWORD_RESET) {
            if(!$student) {
                return $this->apiResponse(null, 'الطالب غير موجود', 404);
            }   
        }

        if($request->validated()['type'] == PhoneVerificationCode::TYPE_REGISTRATION) {
            if($student) {
                return $this->apiResponse(null, 'رقم الهاتف هذا مسجل مسبقاً', 404);
            }   
        }

        $result = app(PhoneVerificationService::class)->sendVerificationCode(
            $request->validated()['phone'],
            $request->validated()['type']
        );

        return $this->apiResponse(
            $result['success'] ? [] : null,
            $result['message'],
            $result['success'] ? 200 : 400
        );
    }

    public function verifyPhone(VerifyPhoneRequest $request): JsonResponse
    {
        $result = app(PhoneVerificationService::class)->verifyCode(
            $request->validated()['phone'],
            $request->validated()['code'],
            $request->validated()['type'],
            false
        );

        return $this->apiResponse(
            $result['success'] ? [] : null,
            $result['message'],
            $result['success'] ? 200 : 400
        );
    }

    public function resetPasswordWithPhone(ResetPasswordWithPhoneRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $phoneVerification = app(PhoneVerificationService::class)->verifyCode(
            $validated['phone'],
            $validated['verification_code'],
            PhoneVerificationCode::TYPE_PASSWORD_RESET
        );

        if (!$phoneVerification['success']) {
            return $this->apiResponse(null, $phoneVerification['message'], 400);
        }

        $student = Student::where('phone', $validated['phone'])->first();

        if (!$student) {
            return $this->apiResponse(null, 'الطالب غير موجود', 404);
        }

        $student->update([
            'password' => Hash::make($validated['password'])
        ]);

        return $this->apiResponse([], 'تم إعادة تعيين كلمة المرور بنجاح', 200);
    }

    public function sendPhoneChangeVerificationCode(SendPhoneChangeVerificationRequest $request): JsonResponse
    {
        try {
            /** @var Student $student */
            $student = auth('student')->user();
            $result = $this->authService->sendPhoneChangeVerificationCode($student, $request->validated()['new_phone']);

            return $this->apiResponse(
                $result['success'] ? [] : null,
                $result['message'],
                $result['status']
            );
        } catch (Exception $e) {
            return $this->apiResponse(null, 'حدث خطأ أثناء إرسال رمز التحقق', 500);
        }
    }

    public function changePhoneNumber(ChangePhoneRequest $request): JsonResponse
    {
        try {
            /** @var Student $student */
            $student = auth('student')->user();
            $result = $this->authService->changePhoneNumber($student, $request->validated());

            if ($result['success']) {
                return $this->apiResponse(
                    $result['student'],
                    $result['message'],
                    $result['status']
                );
            }

            return $this->apiResponse(null, $result['message'], $result['status']);
        } catch (Exception $e) {
            return $this->apiResponse(null, 'حدث خطأ أثناء تغيير رقم الهاتف', 500);
        }
    }
}

