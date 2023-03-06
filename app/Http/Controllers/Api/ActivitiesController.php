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
        $record = SignRecord::where('user_id', $user->id)->where('created_at', '<=', Carbon::now()->endOfDay())->where('created_at', '>=', Carbon::now()->startOfDay())->first();
        if (!$record) {
            $ret = SignRecord::create(['user_id' => $user->id]);
            // 发放奖励
            if ($ret && $user->incrUsableNum()) {
                return result(['sign_record' => 1, 'message' => '签到成功.']);
            }
            return result(['sign_record' => 0, 'message' => '签到失败,请稍后重试.']);
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
