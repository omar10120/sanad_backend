<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LessonVideoResource;
use App\Http\Resources\SubjectVideoResource;
use App\Http\Resources\TeacherResource;
use App\Http\Resources\UnitResource;
use App\Http\Resources\YoutubeLinkVideoResource;
use App\Services\ApiSubjectVideoService;
use App\Http\Resources\TypeHasSubjectVideoResource;
use Illuminate\Http\JsonResponse;
use App\Services\SubjectVideoService;


use Illuminate\Support\Facades\Auth;

class ApiSubjectVideoController extends Controller
{
    use ApiResponseTrait;
    protected SubjectVideoService $subjectService;
    protected ApiSubjectVideoService $apiSubjectVideoService;

    public function __construct(SubjectVideoService $subjectService, ApiSubjectVideoService $apiSubjectVideoService)
    {
        $this->subjectService = $subjectService;
        $this->apiSubjectVideoService = $apiSubjectVideoService;
    }

    public function index(): JsonResponse
    {
        $student = Auth::user();
        $subjectVideos = $this->apiSubjectVideoService->getSubjectVideosForStudentType($student->type_id);
        $type_has_subject = $this->subjectService->getTypeHasSubjectVideoRelationshipsByType(Auth::user()->type->id);
        $data = [
            'subject_videos' => SubjectVideoResource::collection($subjectVideos),
            'subject_certificate' => TypeHasSubjectVideoResource::collection($type_has_subject),
        ];

        return $this->apiResponse($data, 'جميع مواد الكورسات والمعلمين', 200);
    }

    public function show(int $id): JsonResponse
    {
        $subjectVideo = $this->apiSubjectVideoService->findSubjectVideo($id);

        if (!$subjectVideo) {
            return $this->apiResponse(null, 'مادة الكورس غير موجودة', 400);
        }

        return $this->apiResponse(
            new SubjectVideoResource($subjectVideo),
            'مادة الكورس ' . $subjectVideo->id,
            200
        );
    }

    public function teachers(int $id): JsonResponse
    {
        $subjectVideo = $this->apiSubjectVideoService->findSubjectVideo($id);

        if (!$subjectVideo) {
            return $this->apiResponse(null, 'مادة الكورس غير موجودة', 400);
        }

        $teachers = $this->apiSubjectVideoService->getTeachers($id);

        return $this->apiResponse(
            TeacherResource::collection($teachers),
            'المعلمين في مادة الكورس ' . $subjectVideo->id,
            200
        );
    }

    public function sync(int $id): JsonResponse
    {
        $subjectVideo = $this->apiSubjectVideoService->findSubjectVideo($id);

        if (!$subjectVideo) {
            return $this->apiResponse(null, 'مادة الكورس غير موجودة', 400);
        }

        $student = Auth::user();
        $is_locked = !$this->apiSubjectVideoService->studentHasAccess($subjectVideo, $student->id);
        $data = $this->apiSubjectVideoService->getAllSubjectVideoData($id, $is_locked);

        $transformed = [
            'subject_video' => new SubjectVideoResource($data['subject_video'], $is_locked),
            'teachers' => TeacherResource::collection($data['teachers']),
            'units' => UnitResource::collection(
                $data['units']->map(function ($unit) use ($is_locked, $student) {
                    $unitLocked = $is_locked || !$this->apiSubjectVideoService->studentHasUnitAccess($student->id, $unit->id);

                    return new UnitResource($unit, $unitLocked);
                })
            ),
            'lesson_videos' => LessonVideoResource::collection($data['lesson_videos']),
            'youtube_links' => YoutubeLinkVideoResource::collection($data['youtube_links']),
            'is_locked' => $is_locked,
        ];

        $message = $is_locked
            ? 'معاينة مادة الكورس ' . $subjectVideo->id
            : 'جميع بيانات مادة الكورس ' . $subjectVideo->id;

        return $this->apiResponse($transformed, $message, 200);
    }
}
