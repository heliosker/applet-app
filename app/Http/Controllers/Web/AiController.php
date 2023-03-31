<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Orhanerday\OpenAi\OpenAi;
use App\Http\Controllers\Controller;

class AiController extends Controller
{

    public function prepare(Request $request)
    {

    }

    public function completions(Request $request)
    {

        $input = $request->all();

        $validator = Validator::make($input, [
            'prompt' => 'required',
            'scene' => 'int|max:2|min:1',
        ]);

        if ($validator->fails()) {
            return error((string)$validator->errors()->first(), 422);
        }

        $openAi = new OpenAi(config('open.openai_api_key'));

        $messages[] = [
            "role" => "user",
            "content" => $input['prompt']
        ];

        $opts = [
            'model' => config('open.openai_model', 'gpt-3.5-turbo'),
            'messages' => $messages,
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

        $openAi->chat($opts, function ($curl_info, $data) {
            $complete = json_decode($data);
            if (isset($complete->error)) {
                echo "event: error" . PHP_EOL;
                echo "data: {$complete->error->message}";
            } else {
                echo $data;
            }
            echo PHP_EOL;
            ob_flush();
            flush();
            return strlen($data);
        });
    }
}
