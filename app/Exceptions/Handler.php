<?php

namespace App\Exceptions;

use de\xqueue\maileon\api\client\MaileonAPIException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Throwable;


class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
        $this->renderable(function (Throwable $exception, Request $request) {
            if ($request->is('api/*')) {
                if ($exception instanceof RequestException){
                    return response()
                        ->json(
                            ['message' => $exception->getMessage()],
                            Response::HTTP_INTERNAL_SERVER_ERROR
                        );
                }
                if ($exception instanceof MaileonAPIException){
                    return response()
                        ->json(
                            ['message' => 'Maileon API call failed with message: ' . $exception->getMessage()],
                            Response::HTTP_INTERNAL_SERVER_ERROR
                        );
                }
                if ($exception instanceof JsonException){
                    return response()
                        ->json(
                            ['message' => $exception->getMessage()],
                            Response::HTTP_BAD_REQUEST
                        );
                }
                if ($exception instanceof MethodNotAllowedHttpException){
                    return response()
                        ->json(
                            ['message' => 'Incorrect route. Try something else'],
                            Response::HTTP_NOT_FOUND
                        );
                }

            }

        });
    }
}
