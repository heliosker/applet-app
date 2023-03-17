<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

if (!function_exists('result')) {
    /**
     * 返回数据
     *
     * @param object|array|string|null $data 数据
     * @param int $status 状态码
     * @return JsonResponse
     */
    function result(object|array|string $data = null, int $status = 200): JsonResponse
    {
        if ($data instanceof LengthAwarePaginator || $data instanceof AnonymousResourceCollection) {
            return new JsonResponse([
                'code' => $status,
                'data' => [
                    'items' => $data->items(),
                    'meta' => [
                        'current_page' => $data->currentPage(),
                        'from' => $data->firstItem(),
                        'per_page' => $data->perPage(),
                        'to' => $data->lastItem(),
                        'last_page' => $data->lastPage(),
                        'total' => $data->total(),
                    ],
                ],
            ], $status);
        } else {
            return new JsonResponse([
                'code' => $status,
                'data' => $data ? $data : []
            ], $status);
        }
    }
}

if (!function_exists('error')) {
    /**
     * 返回错误
     *
     * @param string $msg 错误信息
     * @param int $status 状态码
     * @return JsonResponse
     */
    function error(string $msg = '参数错误', int $status = 200): JsonResponse
    {
        return response()->json(
            ['code' => $status, 'message' => $msg],
            $status
        );
    }
}

