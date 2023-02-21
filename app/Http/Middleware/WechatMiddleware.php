<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WechatMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');


        $user = auth('api')->user();
        dump($user);die;

        if(!$user) {
            return error('请先登陆', 401);
        }

        if($user->status !== 1) {
            return error('当前账户暂时无法使用，请联系客服');
        }

        $request->user  = $user;
        $request->uid   = $user->getKey();

        return $next($request);
    }
}
