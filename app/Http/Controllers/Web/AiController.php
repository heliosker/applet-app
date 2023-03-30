<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Orhanerday\OpenAi\OpenAi;
use App\Http\Controllers\Controller;

class AiController extends Controller
{

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

        $opts = [
            'prompt' => $input['prompt'],
//            'model' => config('open.openai_model', 'gpt-3.5-turbo'),
            'temperature' => 0.9,
            "max_tokens" => 150,
            "frequency_penalty" => 0,
            "presence_penalty" => 0.6,
            "stream" => true,
        ];

        header('Content-type: text/event-stream');
        header('Cache-Control: no-cache');

        if ($baseUrl = config('open.openai_base_url')) {
            $openAi->setBaseURL($baseUrl);
        }


        $openAi->completion($opts, function ($curl_info, $data) {
            echo $data . "<br><br>";
            echo PHP_EOL;
            ob_flush();
            flush();
            return strlen($data);
        });
    }
}
