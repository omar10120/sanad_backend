<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeacherResource;
use App\Http\Resources\UnitWithContentResource;
use App\Services\ApiSubjectVideoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ApiTeacherController extends Controller
{
    use ApiResponseTrait;

    protected ApiSubjectVideoService $apiSubjectVideoService;

    public function __construct(ApiSubjectVideoService $apiSubjectVideoService)
    {
        $this->apiSubjectVideoService = $apiSubjectVideoService;
    }

    public function show(int $id): JsonResponse
    {
        $teacher = $this->apiSubjectVideoService->findTeacher($id);

        if (!$teacher) {
            return $this->apiResponse(null, 'المعلم غير موجود', 400);
        }

        return $this->apiResponse(
            new TeacherResource($teacher),
            'المعلم ' . $teacher->id,
            200
        );
    }

    public function units(int $id): JsonResponse
    {
        $teacher = $this->apiSubjectVideoService->findTeacher($id);

        if (!$teacher) {
            return $this->apiResponse(null, 'المعلم غير موجود', 400);
        }

        $student = Auth::user();
        $units = $this->apiSubjectVideoService->getUnitsByTeacherWithContent($id);

        $transformedUnits = $units->map(function ($unit) use ($student) {
            $isLocked = $this->apiSubjectVideoService->isUnitLocked($student->id, $unit->id);

            return (new UnitWithContentResource($unit, $isLocked))->resolve();
        });

        return $this->apiResponse(
            $transformedUnits,
            'الوحدات للمعلم ' . $teacher->id,
            200
        );
    }
}
