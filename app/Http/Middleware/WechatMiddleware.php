<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WechatMiddleware
{

    /**
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');

        if (empty($token)) {
            return error('未授权，请登录后再访问', 401);
        }

        $user = auth('api')->user();
        if (!$user) {
            return error('Token 已失效，请重新获取', 401);
        }

        if ($user->block_status == 1) {
            return error('当前账户暂时无法使用，请联系客服', 403);
        }

        $request->user = $user;
        $request->uid = $user->getKey();

        return $next($request);
    }
}
