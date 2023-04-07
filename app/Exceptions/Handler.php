<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Exception|Throwable $e
     * @return JsonResponse
     * @throws Throwable
     */
    public function render($request, Exception|Throwable $e)
    {
        try {
            // 参数验证错误的异常，我们需要返回 400 的 http code 和一句错误信息
            if ($e instanceof ValidationException) {
                return error(array_first(array_collapse($e->errors())), 422);
            }

            // 用户认证的异常，我们需要返回 401 的 http code 和错误信息
            if ($e instanceof AuthenticationException) {
                return error($e->getMessage(), 401);
            }

            if ($request->isJson()) {
                return error($e->getMessage(), 422);
            }

        } catch (Exception $e) {
            return error($e->getMessage(), 500);
        }
        return parent::render($request, $e);
    }


}
