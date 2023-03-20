<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UsageRecordsResource;
use App\Http\Resources\Api\UserResource;
use App\Http\Resources\Api\UserTidyResource;
use App\Models\UsageRecords;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    /**
     * 个人中心
     *
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        $user = auth('api')->user();
        return result(new UserResource($user));
    }


    /**
     * 获取精简信息
     *
     * @return JsonResponse
     */
    public function part(): JsonResponse
    {
        $user = auth('api')->user();
        return result(new UserTidyResource($user));
    }

    /**
     * Usages records
     * @return JsonResponse
     */
    public function usages(): JsonResponse
    {
        $user = auth('api')->user();
        return result(UsageRecordsResource::collection(UsageRecords::where('user_id', $user->id)->paginate()));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'nick_name' => 'string',
            'gender' => 'int',
            'language' => 'string|nullable',
            'city' => 'string|nullable',
            'province' => 'string|nullable',
            'country' => 'string|nullable',
            'avatar_url' => 'string',
        ]);

        if ($validator->fails()) {
            return error((string)$validator->errors()->first(), 422);
        }

        $user = auth('api')->user();

        if ($user->update($input)) {
            return result(new UserResource($user));
        }
        return error('Noting.', 200);
    }

}
