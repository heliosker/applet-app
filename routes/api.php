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

    Route::get('token/test', [AuthorizationsController::class, 'test']);

    Route::get('upload/token', [UploadController::class, 'token']);

    Route::get('index/banner', [IndexController::class, 'banner']);
    Route::get('index/share', [IndexController::class, 'share']);

    Route::post('auth/login', [AuthorizationsController::class, 'login']);

    Route::post('activities/invite', [ActivitiesController::class, 'invite']);

});


Route::namespace('Api')->middleware(['auth:api'])->prefix('v1')->group(function () {

    Route::post('token/validate', [AuthorizationsController::class, 'check']);

    Route::get('auth/members', [UserController::class, 'show']);
    Route::get('members/parts', [UserController::class, 'part']);
    Route::post('auth/members', [UserController::class, 'update']);


    // 聊天
    Route::post('chat/completions', [OpenAiController::class, 'completions']);

    // 活动
    Route::post('activities/punch-in', [ActivitiesController::class, 'punchIn']);
    Route::get('chat/watch-ads', [ActivitiesController::class, 'watchAds']);

});


