<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Code\CheckCodeRequest;
use App\Http\Resources\CodeResource;
use App\Services\ApiCodeService;
use Exception;
use Illuminate\Http\JsonResponse;
use stdClass;

class ApiCodeController extends Controller
{
    use ApiResponseTrait;
    protected ApiCodeService $apiCodeService;

    public function __construct(ApiCodeService $apiCodeService)
    {
        $this->apiCodeService = $apiCodeService;
    }

    public function checkCode(CheckCodeRequest $request): JsonResponse
    {
        try {
            $student = $this->apiCodeService->getCurrentStudent();
            $validated = $request->validated();
            $result = $this->apiCodeService->validateAndAssignCode($validated['code'] ?? null, $student->id);
            if ($result['success']) {
                $data = new CodeResource($result['data']);
                return $this->apiResponse($data, $result['message'], $result['status']);
            }
            return $this->apiResponse(null, $result['message'], $result['status']);
        } catch (Exception $e) {
            return $this->apiResponse(null, $e->getMessage(), 500);
        }
    }

    public function Codes(): JsonResponse
    {
        try {
            $student = $this->apiCodeService->getCurrentStudent();
            $result = $this->apiCodeService->getStudentCodesFormatted($student->id);

            if ($result['success']) {
                if ($result['data'] instanceof stdClass) {
                    return $this->apiResponse($result['data'], $result['message'], $result['status']);
                }

                $data = [
                    'count' => $result['data']['count'],
                    'codes' => CodeResource::collection($result['data']['codes'])
                ];

                return $this->apiResponse($data, $result['message'], $result['status']);
            }
            return $this->apiResponse(null, $result['message'], $result['status']);
        } catch (Exception $e) {
            return $this->apiResponse(null, $e->getMessage(), 500);
        }
    }
}
