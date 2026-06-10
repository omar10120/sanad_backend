<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use stdClass;

class ApiCodeService
{
    protected CodeService $codeService;

    public function __construct(CodeService $codeService)
    {
        $this->codeService = $codeService;
    }

    /**
     * Check and validate a code for a student
     *
     * @param string $code
     * @param int $studentId
     * @return array
     */
    public function validateAndAssignCode(string $code, int $studentId): array
    {
        $codeModel = $this->codeService->checkCode($code);

        if (!$codeModel) {
            return [
                'success' => false,
                'message' => 'الكود غير موجود في قاعدة البيانات.',
                'status' => 404
            ];
        }

        if ($this->codeService->isCodeUsedByAnotherStudent($codeModel, $studentId)) {
            return [
                'success' => false,
                'message' => 'هذا الكود مستخدم من قبل طالب آخر.',
                'status' => 403
            ];
        }

        $assigned = $this->codeService->assignCodeToStudent($codeModel, $studentId);

        if ($assigned) {
            return [
                'success' => true,
                'data' => $codeModel,
                'message' => 'تم العثور على الكود بنجاح.',
                'status' => 200
            ];
        }

        return [
            'success' => false,
            'message' => 'فشل في تعيين الكود للطالب.',
            'status' => 500
        ];
    }

    /**
     * Get codes used by a student with formatted response
     *
     * @param int $studentId
     * @return array
     */
    public function getStudentCodesFormatted(int $studentId): array
    {
        $usedCodes = $this->codeService->getStudentCodes($studentId);

        if ($usedCodes->isEmpty()) {
            return [
                'success' => true,
                'data' => new stdClass(),
                'message' => 'لا توجد أكواد مستخدمة من قبل هذا الطالب.',
                'status' => 200
            ];
        }

        return [
            'success' => true,
            'data' => [
                'count' => $usedCodes->count(),
                'codes' => $usedCodes
            ],
            'message' => 'تم العثور على الأكواد بنجاح.',
            'status' => 200
        ];
    }

    /**
     * Get current authenticated student
     *
     * @return User|Authenticatable|null
     */
    public function getCurrentStudent(): User|Authenticatable|null
    {
        return Auth::user();
    }
}
