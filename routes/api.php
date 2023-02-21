<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
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




// 通用接口
Route::namespace('Api')->middleware('api')->prefix('v1')->group(function () {

    Route::get('token/test', [AuthorizationsController::class, 'test']);


    Route::post('auth/login', [AuthorizationsController::class, 'login']);
    Route::get('auth/members', [AuthorizationsController::class, 'members']);


    // 聊天
    Route::post('chat/ask', [OpenAiController::class, 'ask']);

    // 活动
    Route::post('chat/punch-in', [ActivitiesController::class, 'punchIn']);
    Route::get('chat/watch-ads', [ActivitiesController::class, 'watchAds']);

});

