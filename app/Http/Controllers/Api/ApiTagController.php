<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionResource;
use App\Http\Resources\TagResource;
use App\Services\TagService;
use Illuminate\Http\JsonResponse;

class ApiTagController extends Controller
{
    use ApiResponseTrait;

    protected TagService $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    public function index(): JsonResponse
    {
        $tags = TagResource::collection($this->tagService->getAllTags());
        return $this->apiResponse($tags, 'جميع العلامات', 200);
    }

    public function show($id): JsonResponse
    {
        $tag = $this->tagService->findTag($id);

        if (!$tag) {
            return $this->apiResponse(null, 'العلامة غير موجودة', 400);
        }

        $tag = new TagResource($tag);
        return $this->apiResponse($tag, 'العلامة ' . $tag->id, 200);
    }

    public function questions($id): JsonResponse
    {
        $tag = $this->tagService->getTagWithQuestions($id);

        if (!$tag) {
            return $this->apiResponse(null, 'العلامة غير موجودة', 400);
        }

        $questions = QuestionResource::collection($tag->questions);
        return $this->apiResponse($questions, 'الأسئلة في العلامة ' . $tag->id, 200);
    }
}
