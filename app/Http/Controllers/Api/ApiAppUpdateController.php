<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AppUpdateService;
use App\Http\Requests\AppUpdate\CheckAppUpdateRequest;
use Illuminate\Http\Request;

class ApiAppUpdateController extends Controller
{
    use ApiResponseTrait;

    protected $appUpdateService;

    public function __construct(AppUpdateService $appUpdateService)
    {
        $this->appUpdateService = $appUpdateService;
    }

    /**
     * Check for app updates
     *
     * @param CheckAppUpdateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function check(CheckAppUpdateRequest $request)
    {
        $data = $this->appUpdateService->checkForUpdates($request);

        if (!$data['has_update'] && !$data['latest_version']) {
            return $this->apiResponse([], 'لم يتم العثور على تحديثات', 200);
        }

        return $this->apiResponse($data, 'أحدث تحديث', 200);
    }
}
