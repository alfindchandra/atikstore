<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Throwable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
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
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Handle 404 Not Found
        if ($exception instanceof NotFoundHttpException) {
            return response()->view('errors.404', [
                'exception' => $exception
            ], 404);
        }

        // Handle 403 Forbidden
        if ($exception instanceof AccessDeniedHttpException) {
            return response()->view('errors.403', [
                'exception' => $exception
            ], 403);
        }

        // Handle Authentication Exception (401)
        if ($exception instanceof AuthenticationException) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->guest(route('login'));
        }

        // Handle 500 Internal Server Error
        if ($this->shouldRenderCustomErrorPage($exception)) {
            return response()->view('errors.500', [
                'exception' => $exception
            ], 500);
        }

        return parent::render($request, $exception);
    }

    /**
     * Determine if we should render a custom error page
     *
     * @param \Throwable $exception
     * @return bool
     */
    protected function shouldRenderCustomErrorPage(Throwable $exception): bool
    {
        // Don't show custom error page in debug mode for better debugging
        if (config('app.debug')) {
            return false;
        }

        // Show custom error page for server errors
        return $exception instanceof \Error || 
               $exception instanceof \Exception;
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest($exception->redirectTo() ?? route('login'))
            ->with('error', 'Silakan login untuk mengakses halaman ini.');
    }
}