<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LessonVideoResource;
use App\Http\Resources\UnitResource;
use App\Services\ApiSubjectVideoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ApiUnitController extends Controller
{
    use ApiResponseTrait;

    protected ApiSubjectVideoService $apiSubjectVideoService;

    public function __construct(ApiSubjectVideoService $apiSubjectVideoService)
    {
        $this->apiSubjectVideoService = $apiSubjectVideoService;
    }

    public function show(int $id): JsonResponse
    {
        $unit = $this->apiSubjectVideoService->findUnit($id);

        if (!$unit) {
            return $this->apiResponse(null, 'الوحدة غير موجودة', 400);
        }

        $student = Auth::user();
        $isLocked = $this->apiSubjectVideoService->isUnitLocked($student->id, $unit->id);

        return $this->apiResponse(
            new UnitResource($unit, $isLocked),
            'الوحدة ' . $unit->id,
            200
        );
    }

    public function lessonVideos(int $id): JsonResponse
    {
        $unit = $this->apiSubjectVideoService->findUnit($id);

        if (!$unit) {
            return $this->apiResponse(null, 'الوحدة غير موجودة', 400);
        }

        $student = Auth::user();
        $isLocked = $this->apiSubjectVideoService->isUnitLocked($student->id, $unit->id);
        $lessonVideos = $this->apiSubjectVideoService->getLessonVideosByUnit($id, $isLocked);

        $data = [
            'lesson_videos' => LessonVideoResource::collection($lessonVideos),
            'is_locked' => $isLocked,
        ];

        $message = $isLocked
            ? 'معاينة دروس الوحدة ' . $unit->id
            : 'دروس الوحدة ' . $unit->id;

        return $this->apiResponse($data, $message, 200);
    }
}
