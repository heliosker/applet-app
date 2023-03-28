<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ChatHistoryResource;
use App\Http\Resources\Api\ImageHistoryResource;
use App\Models\ChatHistory;
use App\Models\ImageHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Orhanerday\OpenAi\OpenAi;
use Illuminate\Http\JsonResponse;

class OpenAiController extends Controller
{

    /**
     * Chat
     *
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
                'scene' => 'int|max:2|min:1',
            ]);

            if ($validator->fails()) {
                return error((string)$validator->errors()->first(), 422);
            }

            // 验证次数
            $user = auth('api')->user();
            if (!$user->checkUsable()) {
                return error('您的次数已用完，请先获取次数。', 403);
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
            if ($ch = ChatHistory::write($complete, $input['prompt'], $user->id, $input['scene'] ?? ChatHistory::SCENE_CHAT)) {
                // 扣减次数
                $user->decrUsableNum(1, '文字AI');
                return result(new ChatHistoryResource($ch));
            }

            return error('服务器睡着了，请稍后再试', 500);
        } catch (\Exception $e) {
            return error($e->getMessage(), 500);
        }
    }

    /**
     * Chat history
     * @return JsonResponse
     */
    public function histories(): JsonResponse
    {
        $user = auth('api')->user();

        $histories = ChatHistory::where('user_id', $user->id)->orderBy('id', 'desc')->paginate();
        return result(ChatHistoryResource::collection($histories));
    }

    /**
     * 绘图AI
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function generations(Request $request): JsonResponse
    {
        try {

            $input = $request->all();

            $validator = Validator::make($input, [
                'prompt' => 'required',
                'n' => 'int|max:10|min:1',
                'size' => [
                    'required',
                    Rule::in(['256x256', '512x512', '1024x1024']),
                ],
                'response_format' => [
                    'required',
                    Rule::in(['url', 'b64_json']),
                ],
//            'chat_id' => 'string',
            ]);

            if ($validator->fails()) {
                return error((string)$validator->errors()->first(), 422);
            }


            $openAiKey = config('open.openai_api_key');
            $openAi = new OpenAi($openAiKey);
            if ($baseUrl = config('open.openai_base_url')) {
                $openAi->setBaseURL($baseUrl);
            }
            $complete = $openAi->image($input);

            // 响应错误
            $completeArr = json_decode($complete, true);
            if (isset($completeArr['error'])) {
                return error($completeArr['error']['message'], 500);
            }

            $user = auth('api')->user();
            if ($ih = ImageHistory::write($input, $completeArr, $user->id)) {
                $user->decrUsableNum(1, '图片AI');
                return result(new ImageHistoryResource($ih));
            }

            return error('服务器睡着了，请稍后再试', 500);

        } catch (\Exception $e) {
            return error($e->getMessage(), 500);
        }
    }


}
