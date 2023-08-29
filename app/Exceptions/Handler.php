<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Client\RequestException;
use Symfony\Component\HttpFoundation\Response;
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
        $this->renderable(function (Throwable $exception, $request) {
            if ($request->is('api/*')) {
                if ($exception instanceof RequestException){
                    return response()
                        ->json(['message' => 'External API call failed'], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                return response()
                    ->json(['message' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return parent::render($request, $exception);
        });
    }
}
