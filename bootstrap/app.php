<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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

        // Handle 403 errors
        $exceptions->render(function (AuthorizationException $e, $request) {
            return response()->view('errors.403', [], 403);
        });
        
        // Handle 404 errors
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            return response()->view('errors.404', [], 404);
        });

        // Handle 500 errors
        $exceptions->render(function (Exception $e, $request) {
            if ($e instanceof \ErrorException || $e instanceof \Error || $e instanceof \Throwable) {
                return response()->view('errors.500', [
                    'exception' => config('app.debug') ? $e : null,
                ], 500);
            }
        });
    })->create();
