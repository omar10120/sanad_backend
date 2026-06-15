<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LessonVideoResource;
use App\Http\Resources\YoutubeLinkVideoResource;
use App\Services\ApiSubjectVideoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ApiLessonVideoController extends Controller
{
    use ApiResponseTrait;

    protected ApiSubjectVideoService $apiSubjectVideoService;

    public function __construct(ApiSubjectVideoService $apiSubjectVideoService)
    {
        $this->apiSubjectVideoService = $apiSubjectVideoService;
    }

    public function show(int $id): JsonResponse
    {
        $lessonVideo = $this->apiSubjectVideoService->findLessonVideo($id);

        if (!$lessonVideo) {
            return $this->apiResponse(null, 'درس الفيديو غير موجود', 400);
        }

        return $this->apiResponse(
            new LessonVideoResource($lessonVideo),
            'درس الفيديو ' . $lessonVideo->id,
            200
        );
    }

    public function youtubeLinks(int $id): JsonResponse
    {
        $lessonVideo = $this->apiSubjectVideoService->findLessonVideo($id);

        if (!$lessonVideo) {
            return $this->apiResponse(null, 'درس الفيديو غير موجود', 400);
        }

        $student = Auth::user();
        $isLocked = $this->apiSubjectVideoService->isUnitLocked($student->id, $lessonVideo->unit_id);
        $youtubeLinks = $this->apiSubjectVideoService->getYoutubeLinksByLessonVideo($id, $isLocked);

        $data = [
            'youtube_links' => YoutubeLinkVideoResource::collection($youtubeLinks),
            'is_locked' => $isLocked,
        ];

        $message = $isLocked
            ? 'معاينة فيديوهات الدرس ' . $lessonVideo->id
            : 'فيديوهات الدرس ' . $lessonVideo->id;

        return $this->apiResponse($data, $message, 200);
    }
}
