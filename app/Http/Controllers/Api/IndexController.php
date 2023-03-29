<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class IndexController extends Controller
{

    /**
     * Banner
     * @return JsonResponse
     */
    public function banner(): JsonResponse
    {
        $data = [
            ['banner_url' => 'https://images.helloadmin.cn/images/banner/banner_url_03.png', 'title' => 'title 01'],
            ['banner_url' => 'hhttps://images.helloadmin.cn/images/banner/banner_url_03.png', 'title' => '家人们，ChatGPT体验下？'],
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
