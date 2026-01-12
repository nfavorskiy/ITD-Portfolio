<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle CSRF token mismatch
        $exceptions->render(function (TokenMismatchException $e, $request) {
            return redirect()->back()->withInput()->with('csrf_error', 'Your session expired. Please refresh and try again.');
        });

        // Handle authentication exceptions
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->guest(route('login'));
        });

        // Handle AuthorizationException (from Gate/Policy)
        $exceptions->render(function (AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden.'], 403);
            }
            return response()->view('errors.403', [], 403);
        });

        // Handle HTTP exceptions (abort(403), abort(404), etc.)
        $exceptions->render(function (HttpException $e, $request) {
            $status = $e->getStatusCode();
            
            if ($status === 403) {
                return response()->view('errors.403', [], 403);
            }
            
            if ($status === 404) {
                return response()->view('errors.404', [], 404);
            }
            
            if ($status === 500) {
                return response()->view('errors.500', [
                    'exception' => config('app.debug') ? $e : null,
                ], 500);
            }
            
            // Let Laravel handle other HTTP status codes
            return null;
        });
    })->create();