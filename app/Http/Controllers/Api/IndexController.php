<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class IndexController extends Controller
{

    /**
     * Version
     * @return JsonResponse
     */
    public function version(): JsonResponse
    {
        $data = [
            "latest" => [
                'ai_qa' => config('AI_QA', 0),
                'baby_name' => config('BABY_NAME', 0),
                'ai_draw' => config('AI_DRAW', 0),
            ]];
        return result($data);
    }

    /**
     * Banner
     * @return JsonResponse
     */
    public function banner(): JsonResponse
    {
        $data = [
            ['banner_url' => 'https://images.helloadmin.cn/images/banner/banner_url_03.png', 'title' => 'title 01'],
            ['banner_url' => 'https://images.helloadmin.cn/images/banner/banner_url_03.png', 'title' => '家人们，ChatGPT体验下？'],
        ];

        return result($data);
    }

    /**
     * Share
     * @return JsonResponse
     */
    public function share(): JsonResponse
    {
        $data = [
            'title' => '家人们，ChatGPT体验下？',
            'image_url' => 'https://images.helloadmin.cn/images/share/share_url_01.png',
            'path' => '/pages/index/index'
        ];

        return result($data);
    }


}
