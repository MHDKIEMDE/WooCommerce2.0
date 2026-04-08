<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'active' => \App\Http\Middleware\EnsureUserIsActive::class,
            'role'   => \App\Http\Middleware\EnsureUserHasRole::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Alerte stock critique — tous les jours à 8h00
        $schedule->command('app:stock-alert')->dailyAt('08:00');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Routes API → toujours JSON, jamais de redirection HTML
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non authentifié. Veuillez vous connecter.',
                ], 401);
            }
        });

        $exceptions->render(function (AuthorizationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Action non autorisée.',
                ], 403);
            }
        });
    })->create();
