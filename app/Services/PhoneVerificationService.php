<?php

namespace App\Services;

use App\Models\PhoneVerificationCode;
use Exception;
use Illuminate\Support\Facades\Log;

class PhoneVerificationService
{
    protected MTNSMSService $smsService;

    public function __construct(MTNSMSService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function sendVerificationCode(string $phone, string $type): array
    {
        try {
            $verificationCode = PhoneVerificationCode::generate($phone, $type);

            // إرسال الرسالة عبر MTN
            // $message = $this->getSMSMessage($verificationCode->code, $type);
            // $sent = $this->smsService->sendSMS($phone, $message, 'ar');

            // if (!$sent) {
                // throw new Exception('Failed to send SMS via MTN service');
            // }

            // Log::info("Verification code sent via MTN", [
                // 'phone' => $phone,
                // 'type' => $type
            // ]);

            return [
                'success' => true,
                'message' => 'تم إرسال رمز التحقق بنجاح',
                'expires_at' => $verificationCode->expires_at,
            ];
        } catch (Exception $e) {
            Log::error("Failed to send verification code", [
                'phone' => $phone,
                'type' => $type,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'فشل إرسال رمز التحقق',
            ];
        }
    }

    public function verifyCode(string $phone, string $code, string $type, bool $mark = false): array
    {
        $verificationCode = PhoneVerificationCode::where('phone', $phone)
            ->where('code', $code)
            ->where('type', $type)
            ->first();

        if (!$verificationCode) {
            return [
                'success' => false,
                'message' => 'رمز التحقق غير صحيح',
            ];
        }

        if (!$verificationCode->isValid()) {
            return [
                'success' => false,
                'message' => $verificationCode->isExpired()
                    ? 'انتهت صلاحية رمز التحقق'
                    : 'تم استخدام رمز التحقق مسبقاً',
            ];
        }

        if($mark) {
            $verificationCode->markAsUsed();
        }

        return [
            'success' => true,
            'message' => 'تم التحقق من الهاتف بنجاح',
        ];
    }

    private function getSMSMessage(string $code, string $type): string
    {
        switch ($type) {
            case PhoneVerificationCode::TYPE_REGISTRATION:
                return "رمز التحقق للتسجيل: {$code}. صالح لمدة 60 دقيقة.";
            case PhoneVerificationCode::TYPE_PASSWORD_RESET:
                return "رمز إعادة تعيين كلمة المرور: {$code}. صالح لمدة 60 دقيقة.";
            case PhoneVerificationCode::TYPE_PHONE_CHANGE:
                return "رمز تغيير رقم الهاتف: {$code}. صالح لمدة 60 دقيقة.";
            default:
                return "رمز التحقق الخاص بك: {$code}. صالح لمدة 60 دقيقة.";
        }
    }

    public function cleanupExpiredCodes(): void
    {
        PhoneVerificationCode::where('expires_at', '<', now())->delete();
    }
}
