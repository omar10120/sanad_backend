<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    public function apiResponse($data = null, $msg = null, $status = null): JsonResponse
    {
        $array=[
            'data' => $data,
            'message' => $msg,
            'status' => $status,
        ];

        return response()->json($array, $status);
//        return response($array, $status);
    }

    public function apiResponseLogin($data = null, $msg = null, $status = null, $access_token = null): JsonResponse
    {
        $array=[
            'data' => $data,
            'message' => $msg,
            'status' => $status,
            'access_token' => $access_token
        ];

        return response()->json($array, $status);
//        return response($array, $status);
    }
}
