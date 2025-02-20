<?php

// /////////////////////////////////////////////////////////////////////////////
// PLEASE DO NOT RENAME OR REMOVE ANY OF THE CODE BELOW.
// YOU CAN ADD YOUR CODE TO THIS FILE TO EXTEND THE FEATURES TO USE THEM IN YOUR WORK.
// /////////////////////////////////////////////////////////////////////////////

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        PlayerNotFoundException::class,
        ProcessTeamSelectionException::class
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register(): void
    {
        $this->renderable(function (Throwable $e) {
            if ($e instanceof PlayerNotFoundException) {
                return new JsonResponse([
                    'message' => $e->getMessage()
                ], $e->getCode() ?: 404);
            }

            if ($e instanceof ProcessTeamSelectionException) {
                return new JsonResponse([
                    'message' => $e->getMessage()
                ], $e->getCode() ?: 500);
            }

            return new JsonResponse([
                'message' => 'Internal server error'
            ], 500);
        });
    }
}
