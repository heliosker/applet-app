<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShareHistories;
use App\Models\SignRecord;
use App\Models\User;
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
            if ($ret && $user->incrUsableNum(1, '签到赠送')) {
                return result(['sign_record' => SignRecord::SIGNED_IN, 'message' => '签到成功.']);
            }
            return result(['sign_record' => SignRecord::NOT_SIGNED_IN, 'message' => '签到失败,请稍后重试.']);
        }
        return error('你今天已签过了.', 422);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function invite(Request $request): JsonResponse
    {
        try {
            $input = $request->all();
            // 受邀者
            $inviteeId = $input['invitee_id'];
            // 邀请人
            $inviterId = $input['inviter_id'];

            if ($inviteeId == $inviterId) {
                return result('Nothing');
//                return error('邀请自己没有奖励哦.', 422);
            }

            $whereData = ['inviter_id' => $inviterId, 'invitee_id' => $inviteeId];
            $inviter = ShareHistories::firstOrCreate($whereData, $whereData);
            if ($inviter->wasRecentlyCreated) {
                $user = User::where('openid', $inviterId)->first();
                $user->incrUsableNum(1, '邀请奖励');
//                return result('邀请奖励已发放.');
                return result('Nothing');
            }

            return result('Nothing');
//            return error('已经邀请过该朋友了，换个人试试.', 422);

        } catch (\Exception $e) {
            return error($e->getMessage(), 500);
        }
    }

    public function watchAds()
    {
        // Todo
    }
}
