<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubjectResource;
use App\Http\Resources\SubjectVideoResource;
use App\Http\Resources\TypeResource;
use App\Models\Type;
use App\Services\TypeService;
use Illuminate\Http\JsonResponse;

class ApiTypeController extends Controller
{
    use ApiResponseTrait;

    protected TypeService $typeService;

    public function __construct(TypeService $typeService)
    {
        $this->typeService = $typeService;
    }

    public function index(): JsonResponse
    {
        $types = TypeResource::collection($this->typeService->getActiveTypes());
        return $this->apiResponse($types, 'جميع الأنواع', 200);
    }

    public function show($id): JsonResponse
    {
        $type = $this->typeService->findType($id);
        if (!$type) {
            return $this->apiResponse(null, 'النوع غير موجود', 400);
        }
        $type = new TypeResource($type);
        return $this->apiResponse($type, 'النوع ' . $type->id, 200);
    }

    public function subjects($id): JsonResponse
    {
        $type = $this->typeService->findType($id);
        if (!$type) {
            return $this->apiResponse(null, 'النوع غير موجود', 400);
        }
        $subjects = SubjectResource::collection($type->subjects);
        return $this->apiResponse($subjects, 'المواد في النوع ' . $type->id, 200);
    }

    public function subjectVideos($id): JsonResponse
    {
        $type = $this->typeService->findType($id);
        if (!$type) {
            return $this->apiResponse(null, 'النوع غير موجود', 400);
        }

        $subjectVideos = SubjectVideoResource::collection(
            $type->subjectVideos()->where('is_active', true)->orderBy('order')->get()
        );

        return $this->apiResponse($subjectVideos, 'مواد الكورسات في النوع ' . $type->id, 200);
    }
}
