
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Do not redirect unauthenticated API requests to a login page.
        $middleware->redirectGuestsTo(
            fn (Request $request) =>
                $request->is('api/*') ? null : '/login'
        );

        $middleware->alias([
            'abilities' => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
            'ability' => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class,
            'api.key' => \App\Http\Middleware\ApiKeyMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request, \Throwable $exception): bool =>
                $request->is('api/*') || $request->expectsJson()
        );

        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $exception, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $msg = $exception->getMessage();
                if (empty($msg) || $msg === 'This action is unauthorized.') {
                    $msg = 'Accès refusé';
                }
                return response()->json([
                    'message' => $msg,
                ], 403);
            }
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $exception, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $msg = $exception->getMessage();
                if (empty($msg) || $msg === 'This action is unauthorized.') {
                    $msg = 'Accès refusé';
                }
                return response()->json([
                    'message' => $msg,
                ], 403);
            }
        });

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $exception, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Non authentifié',
                ], 401);
            }
        });
    })
    ->create();
