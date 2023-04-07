<?php

namespace App\Http\Controllers\Web;

use App\Models\ChatHistory;
use Illuminate\Http\Request;
use Orhanerday\OpenAi\OpenAi;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AiController extends Controller
{

    /**
     * @return StreamedResponse
     */
    public function stream(): StreamedResponse
    {
        return response()->stream(function () {
            $n = 10;
            while ($n >= 0) {
                $n--;
                echo "data: " . time() . "
";
                ob_flush();
                flush();
                sleep(1);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function prepare(Request $request): JsonResponse
    {

//        $input = $request->all();
//
//        $validator = Validator::make($input, [
//            'prompt' => 'required',
//            'messages' => 'required',
//            'scene' => 'int|max:2|min:1',
//        ]);
//
//        if ($validator->fails()) {
//            $err = (string)$validator->errors()->first();
//            return error($err, 422);
//        }

        $user = auth('api')->user();
        // 验证次数
        if (!$user->checkUsable()) {
            return error('您的次数已用完，请先获取次数。', 403);
        }

//        $ret = Cache::put($this->chatKey($user->id), $input);

        // 验证次数
        return result();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function clear(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        $ret = Cache::forget($this->chatKey($user->id));
        return result(['clear' => $ret]);
    }

    public function completions(Request $request)
    {

        $input = $request->all();
        $user = auth('api')->user();

        $stream = '';
        $resp = response()->stream(function () use ($input, $user, $stream) {

            $validator = Validator::make($input, [
                'prompt' => 'required',
                'messages' => 'required',
                'scene' => 'int|max:2|min:1',
            ]);

            if ($validator->fails()) {
                $err = (string)$validator->errors()->first();
                echo "event: error" . PHP_EOL;
                echo "data: Nothing.";
                exit();
            }

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

            if ($baseUrl = config('open.openai_base_url')) {
                $openAi->setBaseURL($baseUrl);
            }

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

        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);

        // 记录
        $answer = '';
        $rspArr = explode("data: ", $stream);
        foreach ($rspArr as $msg) {
            $arr = json_decode(trim($msg), true);
            if (isset($arr['choices'][0]['delta']['content'])) {
                $answer .= $arr['choices'][0]['delta']['content'];
            }
        }

        ChatHistory::webWrite($input['prompt'], $answer, $user->id);

        return $resp;
    }


    /**
     * @param Request $request
     * @return void
     * @throws \Exception
     */
    public function completionsOld(Request $request)
    {

        $user = auth('api')->user();
        $input = Cache::get($this->chatKey($user->id));

        if (empty($input['messages'])) {
            echo "event: error" . PHP_EOL;
            echo "data: Nothing.";
            exit();
        }

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
//        $stream = '';
//        $openAi->chat($opts, function ($curl_info, $data) use (&$stream) {
//            $complete = json_decode($data);
//            if (isset($complete->error)) {
//                echo "event: error" . PHP_EOL;
//                echo "data: {$complete->error->message}";
//            } else {
//                echo $data;
//                $stream .= $data;
//            }
//            echo PHP_EOL;
//            ob_flush();
//            flush();
//            return strlen($data);
//        });

        // 记录
//        $answer = "";
//        $rspArr = explode("data: ", $stream);
//        foreach ($rspArr as $msg) {
//            $arr = json_decode(trim($msg), true);
//            if (isset($arr['choices'][0]['delta']['content'])) {
//                $answer .= $arr['choices'][0]['delta']['content'];
//            }
//        }
//        ChatHistory::webWrite($input['prompt'], $answer, $user->id);
    }


    /**
     * @param int $uid
     * @return string
     */
    protected function chatKey(int $uid): string
    {
        return 'chat:' . $uid;
    }
}
