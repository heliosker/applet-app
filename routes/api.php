<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\IndexController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\OpenAiController;
use App\Http\Controllers\Api\ActivitiesController;
use App\Http\Controllers\Api\AuthorizationsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::namespace('Api')->prefix('v1')->group(function () {

    // 测试
    Route::get('token/test', [AuthorizationsController::class, 'test']);

    // 开关
    Route::get('version', [IndexController::class, 'version']);

    // 图片上传
    Route::get('upload/token', [UploadController::class, 'token']);

    // 轮播
    Route::get('index/banner', [IndexController::class, 'banner']);
    Route::get('index/share', [IndexController::class, 'share']);

    // 登录
    Route::post('auth/login', [AuthorizationsController::class, 'login']);

    // 邀请
    Route::post('activities/invite', [ActivitiesController::class, 'invite']);

});


Route::namespace('Api')->middleware(['auth:api'])->prefix('v1')->group(function () {

    // 令牌检测
    Route::post('token/validate', [AuthorizationsController::class, 'check']);

    // 个人信息
    Route::post('auth/members', [UserController::class, 'update']);
    Route::get('auth/members', [UserController::class, 'show']);
    Route::get('members/parts', [UserController::class, 'part']);
    Route::get('members/usages', [UserController::class, 'usages']);

    // 聊天
    Route::post('chat/completions', [OpenAiController::class, 'completions']);
    Route::post('images/generations', [OpenAiController::class, 'generations']);
    Route::get('chat/histories', [OpenAiController::class, 'histories']);

    // 活动
    Route::post('activities/punch-in', [ActivitiesController::class, 'punchIn']);
    Route::get('chat/watch-ads', [ActivitiesController::class, 'watchAds']);

});


