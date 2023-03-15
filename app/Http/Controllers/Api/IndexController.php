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
            ['banner_url' => 'https://images.helloadmin.cn/banner/banner_url_01.jpg', 'title' => 'title 01'],
            ['banner_url' => 'https://images.helloadmin.cn/banner/banner_url_02.jpg', 'title' => 'title 02'],
            ['banner_url' => 'https://images.helloadmin.cn/banner/banner_url_03.jpg', 'title' => 'title 03'],
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
            'title' => '这里是标题',
            'image_url' => 'https://images.helloadmin.cn/share/share_url_01.jpg',
            'path' => 'path/index'
        ];

        return result($data);
    }


}
