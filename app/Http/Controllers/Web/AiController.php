<?php

namespace App\Http\Controllers\Web;

use App\Models\ChatHistory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Orhanerday\OpenAi\OpenAi;
use App\Http\Controllers\Controller;

class AiController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function prepare(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        // 验证次数
        if (!$user->checkUsable()) {
            return error('您的次数已用完，请先获取次数。', 403);
        }

        // 验证次数
        return result();
    }

    public function completions(Request $request)
    {

        $input = $request->all();

        $validator = Validator::make($input, [
            'prompt' => 'required',
            'messages' => 'required',
            'scene' => 'int|max:2|min:1',
        ]);

        if ($validator->fails()) {
            $err = (string)$validator->errors()->first();
            echo "event: error" . PHP_EOL;
            echo "data: $err";
            exit();
        }

        $user = auth('api')->user();
        $openAi = new OpenAi(config('open.openai_api_key'));

        $opts = [
            'model' => config('open.openai_model', 'gpt-3.5-turbo'),
            'messages' => $input['messages'],
            'temperature' => 0,
            "max_tokens" => 150,
            "frequency_penalty" => 0,
            "presence_penalty" => 0,
            "stream" => true,
        ];

        header('Content-type: text/event-stream');
        header('Cache-Control: no-cache');

        if ($baseUrl = config('open.openai_base_url')) {
            $openAi->setBaseURL($baseUrl);
        }
        $stream = '';
        $openAi->chat($opts, function ($curl_info, $data) use (&$stream) {
            $complete = json_decode($data);
            if (isset($complete->error)) {
                echo "event: error" . PHP_EOL;
                echo "data: {$complete->error->message}";
            } else {
                echo $data;
                $stream .= $data;
            }
            echo PHP_EOL;
            ob_flush();
            flush();
            return strlen($data);
        });

        // 记录
        $answer = "";
        $rspArr = explode("data: ", $stream);
        foreach ($rspArr as $msg) {
            $arr = json_decode(trim($msg), true);
            if (isset($arr['choices'][0]['delta']['content'])) {
                $answer .= $arr['choices'][0]['delta']['content'];
            }
        }

        ChatHistory::webWrite($input['prompt'], $answer, $user->id);
    }
}
