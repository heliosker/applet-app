<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Orhanerday\OpenAi\OpenAi;
use Illuminate\Http\JsonResponse;

class OpenAiController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function ask(Request $request): JsonResponse
    {
        $input = $request->only('prompt');

        $validator = Validator::make($input, [
            'prompt' => 'required',
        ]);

        if ($validator->fails()) {
            return error((string)$validator->errors()->first());
        }

        // 验证次数
        $user = auth('api')->user();
        if (!$user->checkUsable()){
            return error('您的可用次数已用完，请先获取使用次数。');
        }

        // 回复内容；
        $open_ai_key = config('open.openai_api_key');
        $open_ai = new OpenAi($open_ai_key);
        $complete = $open_ai->completion([
            'model' => 'davinci',
            'prompt' => $input['prompt'],
            'temperature' => 0.9,
            'max_tokens' => 150,
            'frequency_penalty' => 0,
            'presence_penalty' => 0.6,
        ]);

        return result(json_decode($complete,true));
    }


}