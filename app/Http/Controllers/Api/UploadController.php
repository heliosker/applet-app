<?php

namespace App\Http\Controllers\Api;

use Qiniu\Auth;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;


class UploadController extends Controller
{

    /**
     * @return JsonResponse
     */
    public function token(): JsonResponse
    {

        $auth = new Auth(config('upload.access_key'), config('upload.secret_key'));
        $token = $auth->uploadToken(config('upload.bucket'));
        return result(['token' => $token]);
    }
}
