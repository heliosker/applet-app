<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;
use Exception;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {

            dump($e->getMessage());die;
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception $exception
     * @return JsonResponse
     * @throws Throwable
     */
    public function render($request, Exception|Throwable $exception): JsonResponse
    {
        try {
            // 参数验证错误的异常，我们需要返回 400 的 http code 和一句错误信息
            if ($exception instanceof ValidationException) {
                return error(array_first(array_collapse($exception->errors())),400);
            }
            // 用户认证的异常，我们需要返回 401 的 http code 和错误信息
            if ($exception instanceof AuthenticationException) {
                return error($exception->getMessage(),401);
            }
        }catch (Exception $exception){
            return error($exception->getMessage(),500);
        }

        return parent::render($request, $exception);
    }


}
