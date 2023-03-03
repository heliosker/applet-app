<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Orhanerday\OpenAi\OpenAi;

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
