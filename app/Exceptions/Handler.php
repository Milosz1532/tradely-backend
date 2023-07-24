<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
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
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // $this->renderable(function (Throwable $e, $request) {
        //     if ($request->expectsJson()) {
        //         $statusCode = 500;
        //         $message = 'Wystąpił błąd. Spróbuj ponownie później.';

        //         if ($e instanceof ValidationException) {
        //             $statusCode = 422;
        //             $message = $e->getMessage();
        //         } elseif ($e instanceof NotFoundHttpException) {
        //             $statusCode = 404;
        //             $message = 'Nie znaleziono żądanego zasobu.';
        //         } elseif ($e instanceof QueryException) {
        //             $statusCode = 500;
        //             $message = 'Wystąpił błąd bazy danych. Spróbuj ponownie później.';
        //         } elseif ($e instanceof AuthenticationException) {
        //             $statusCode = 401;
        //             $message = 'Nieautoryzowany dostęp.';
        //         }
        

        //         return response()->json([
        //             'message' => $message,
        //         ], $statusCode);
        //     }
        // });
    }
}
