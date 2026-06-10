<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class MTNSMSService
{
    private string $baseUrl = 'https://services.mtnsyr.com:7443/general/MTNSERVICES/ConcatenatedSender.aspx';
    private string $username;
    private string $password;
    private string $senderId = 'SANAD';

    public function __construct()
    {
        $this->username = config('services.mtn.username');
        $this->password = config('services.mtn.password');
    }

    /**
     * إرسال رسالة SMS
     */
    public function sendSMS(string $phone, string $message, string $lang = 'ar'): bool
    {
        try {
            // تحويل الرقم من 09xxxxxxxx إلى 9639xxxxxxxx
            $formattedPhone = $this->formatPhoneNumber($phone);

            // تحديد اللغة (0 للعربية، 1 للإنجليزية)
            $langCode = $lang === 'ar' ? '0' : '1';

            // تحويل الرسالة إلى Unicode إذا كانت عربية
            $encodedMessage = $langCode === '0'
                ? $this->convertToUnicode($message)
                : urlencode($message);

            // بناء الـ URL
            $url = $this->baseUrl . '?' . http_build_query([
                    'User' => $this->username,
                    'Pass' => $this->password,
                    'From' => $this->senderId,
                    'Gsm' => $formattedPhone,
                    'Msg' => $encodedMessage,
                    'Lang' => $langCode
                ]);

            // إرسال الطلب
            $response = Http::timeout(30)
                ->withoutVerifying() // في حال كانت هناك مشكلة في SSL Certificate
                ->get($url);

            // التحقق من النجاح
            if ($response->successful()) {
                Log::info('MTN SMS sent successfully', [
                    'phone' => $formattedPhone,
                    'response' => $response->body()
                ]);
                return true;
            }

            Log::error('MTN SMS failed', [
                'phone' => $formattedPhone,
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            return false;

        } catch (Exception $e) {
            Log::error('MTN SMS exception', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * تحويل رقم الهاتف من 09xxxxxxxx إلى 9639xxxxxxxx
     */
    private function formatPhoneNumber(string $phone): string
    {
        // إزالة أي مسافات أو رموز
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // إذا كان الرقم يبدأ بـ 09، نحوله إلى 9639
        if (substr($phone, 0, 2) === '09') {
            return '963' . substr($phone, 1);
        }

        // إذا كان يبدأ بـ 9639، نتركه كما هو
        if (substr($phone, 0, 4) === '9639') {
            return $phone;
        }

        // إذا كان يبدأ بـ +963، نزيل الـ +
        if (substr($phone, 0, 4) === '+963') {
            return substr($phone, 1);
        }

        return $phone;
    }

    /**
     * تحويل النص العربي إلى Unicode Hex
     */
    private function convertToUnicode(string $text): string
    {
        $unicode = '';
        $length = mb_strlen($text, 'UTF-8');

        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($text, $i, 1, 'UTF-8');
            $code = $this->uniord($char);
            $unicode .= sprintf('%04X', $code);
        }

        return $unicode;
    }

    /**
     * الحصول على Unicode code point للحرف
     */
    private function uniord(string $char): int
    {
        $ord0 = ord($char[0]);

        if ($ord0 >= 0 && $ord0 <= 127) {
            return $ord0;
        }

        $ord1 = ord($char[1]);
        if ($ord0 >= 192 && $ord0 <= 223) {
            return ($ord0 - 192) * 64 + ($ord1 - 128);
        }

        $ord2 = ord($char[2]);
        if ($ord0 >= 224 && $ord0 <= 239) {
            return ($ord0 - 224) * 4096 + ($ord1 - 128) * 64 + ($ord2 - 128);
        }

        $ord3 = ord($char[3]);
        if ($ord0 >= 240 && $ord0 <= 247) {
            return ($ord0 - 240) * 262144 + ($ord1 - 128) * 4096 + ($ord2 - 128) * 64 + ($ord3 - 128);
        }

        return 0;
    }

    /**
     * إرسال رسائل متعددة
     */
    public function sendBulkSMS(array $phones, string $message, string $lang = 'ar'): array
    {
        $results = [
            'success' => [],
            'failed' => []
        ];

        foreach ($phones as $phone) {
            if ($this->sendSMS($phone, $message, $lang)) {
                $results['success'][] = $phone;
            } else {
                $results['failed'][] = $phone;
            }

            // إضافة تأخير بسيط بين الرسائل لتجنب Rate Limiting
            usleep(100000); // 0.1 ثانية
        }

        return $results;
    }
}
