<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\UserResource;
use App\Models\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use EasyWeChat\MiniApp\Application;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthorizationsController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'test']]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \EasyWeChat\Kernel\Exceptions\BadResponseException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function login(Request $request): JsonResponse
    {
        /**
         *  1、小程序客户端获取code、nick_name、头像。发起wx.request请求
         * 2、服务器端携带数据（code+appId）向微信服务器端发起请求，获取open_id和session_key
         * 2.1、完成注册或验证逻辑：服务器端注册账号，储存open_id、nick_name和头像信息。
         * 2.2、向微信小程序返回token（token里包含user_id信息）
         * 3、小程序客户端携带token访问服务器接口
         * 3.1、验证服务器端token，如果有效，继续访问
         * 3.2、如果token失效，刷新token，一次有效访问。然后返回新的token（token设置到header中）
         * 3.3、小程序客户更新token。
         */


        $input = $request->only('code');

        $validator = Validator::make($input, [
            'code' => 'required',
        ]);

        if ($validator->fails()) {
            return error((string)$validator->errors()->first(), 422);
        }


        $config = [
            'app_id' => config('open.wechat_app_id'),
            'secret' => config('open.wechat_app_secret'),
            'token' => '',
            'aes_key' => '',

            /**
             * 接口请求相关配置，超时时间等，具体可用参数请参考：
             * https://github.com/symfony/symfony/blob/5.3/src/Symfony/Contracts/HttpClient/HttpClientInterface.php
             */
            'http' => [
                'throw' => true, // 状态码非 200、300 时是否抛出异常，默认为开启
                'timeout' => 5.0,
                // 'base_uri' => 'https://api.weixin.qq.com/', // 如果你在国外想要覆盖默认的 url 的时候才使用，根据不同的模块配置不同的 uri
                'retry' => true, // 使用默认重试配置
                //  'retry' => [
                //      // 仅以下状态码重试
                //      'http_codes' => [429, 500]
                //       // 最大重试次数
                //      'max_retries' => 3,
                //      // 请求间隔 (毫秒)
                //      'delay' => 1000,
                //      // 如果设置，每次重试的等待时间都会增加这个系数
                //      // (例如. 首次:1000ms; 第二次: 3 * 1000ms; etc.)
                //      'multiplier' => 3
                //  ],
            ],
        ];

        $app = new Application($config);
        try {
            $response = $app->getClient()->get("/sns/jscode2session", [
                'appid' => $app->getConfig()->get('app_id'),
                'secret' => $app->getConfig()->get('secret'),
                'js_code' => $input['code'],
                'grant_type' => 'authorization_code'
            ]);
        } catch (\Exception $e) {
            return error($e->getMessage(), 422);
        }

        $data = $response->toArray();
        if (isset($data['errcode'])) {
            return error($data['errmsg'], 422);
        }

        if (!$user = User::where('openid', $data['openid'])->first()) {
            $user = new User;
            $user->openid = $data['openid'];
            $user->session_key = $data['session_key'];

            if (!$user->save()) {
                return error('用户信息更新异常.', 500);
            }
        }

        return result([
            'access_token' => auth('api')->tokenById($user->id),
            'token_type' => 'bearer',
            'open_id' => $user->openid,
            'expires_in' => (string)Carbon::now()->addMinutes(auth('api')->factory()->getTTL())
        ]);
    }


    /**
     * 获取 Token
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function test(Request $request)
    {
        $id = $request->input('id');
        $user = User::where('id', $id)->first();

        return result([
            'access_token' => auth('api')->tokenById($user->id),
            'token_type' => 'bearer',
            'expires_in' => (string)Carbon::now()->addMinutes(auth('api')->factory()->getTTL())
        ]);
    }

    public function check()
    {
        $user = JWTAuth::parseToken()->authenticate();
        return result($user);
    }

}
