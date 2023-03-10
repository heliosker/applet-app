<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ChatHistoryResource;
use App\Models\ChatHistory;
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
    public function completions(Request $request): JsonResponse
    {
        try {

            $input = $request->all();

            $validator = Validator::make($input, [
                'prompt' => 'required',
                'chat_id' => 'string',
            ]);

            if ($validator->fails()) {
                return error((string)$validator->errors()->first(), 422);
            }

            // 验证次数
            $user = auth('api')->user();
            if (!$user->checkUsable()) {
                return error('您的可用次数已用完，请先获取使用次数。', 403);
            }

            // 回复内容；
            $openAiKey = config('open.openai_api_key');
            $openAi = new OpenAi($openAiKey);

            if ($baseUrl = config('open.openai_base_url')) {
                $openAi->setBaseURL($baseUrl);
            }

            // history
            if (isset($input['chat_id'])) {
                $chatId = $input['chat_id'];
                if ($last = ChatHistory::lastMessages($chatId)) {
                    $messages = $last;
                }
            } else {
                // helpful
                $messages[] = [
                    "role" => "system",
                    "content" => "You are a helpful assistant."
                ];
            }

            // input
            $messages[] = [
                "role" => "user",
                "content" => $input['prompt']
            ];

            $complete = $openAi->chat([
                'model' => config('open.openai_model', 'gpt-3.5-turbo'),
                'messages' => $messages,
                'temperature' => 0,
//                'max_tokens' => 4000,
                'frequency_penalty' => 0,
                'presence_penalty' => 0,
            ]);

            // 响应错误
            $completeArr = json_decode($complete, true);
            if (isset($completeArr['error'])) {
                return error($completeArr['error']['message'], 500);
            }

            // chat history
            if ($ch = ChatHistory::write($complete, $input['prompt'], $user->id)) {
                // 扣减次数
                $user->decrUsableNum();
                return result(new ChatHistoryResource($ch));
            }

            return error('服务器睡着了，请稍后再试', 500);
        } catch (\Exception $e) {
            return error($e->getMessage(), 500);
        }
    }


}
