<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Rate limiting aliases
        $middleware->alias([
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'senior.only' => \App\Http\Middleware\SeniorOnly::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Model not found
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The requested record was not found.',
                ], 404);
            }
            return response()->view('errors.404', [
                'exception' => new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('The requested record was not found.'),
            ], 404);
        });

        // Session expired
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session expired. Please refresh the page and try again.',
                ], 419);
            }
            return redirect()->route('tyro-login.login')->with('error', 'Your session has expired. Please log in again.');
        });

        // File too large (post_max_size / upload_max_filesize exceeded)
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\PostTooLargeException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The uploaded file is too large. Maximum size allowed is ' . ini_get('upload_max_filesize') . '.',
                ], 413);
            }
            return redirect()->back()->withErrors(['file' => 'The uploaded file is too large. Maximum size allowed is ' . ini_get('upload_max_filesize') . '.']);
        });

        // Database query errors
        $exceptions->render(function (\Illuminate\Database\QueryException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'A database error occurred. Please try again later.',
                ], 500);
            }
            return response()->view('errors.500', [
                'exception' => new \Symfony\Component\HttpKernel\Exception\HttpException(500, 'A database error occurred. Please try again later.'),
            ], 500);
        });
    })->create();
