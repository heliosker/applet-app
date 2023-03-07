<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SignRecord;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivitiesController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function punchIn(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        if (!SignRecord::isTodaySignedIn($user->id)) {
            $ret = SignRecord::create(['user_id' => $user->id]);
            // 发放奖励
            if ($ret && $user->incrUsableNum()) {
                return result(['sign_record' => SignRecord::SIGNED_IN, 'message' => '签到成功.']);
            }
            return result(['sign_record' => SignRecord::NOT_SIGNED_IN, 'message' => '签到失败,请稍后重试.']);
        }
        return error('你今天已签过了.', 422);
    }

    public function invite(Request $request)
    {
        dump($request->all());

    }

    public function watchAds()
    {

    }
}
