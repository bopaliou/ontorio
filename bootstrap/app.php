<?php

use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$apiRoutePattern = 'api/*';

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Ajouter SecurityHeaders à tous les requêtes web
        $middleware->web(append: [
            SecurityHeaders::class,
        ]);

        $middleware->alias([
            'role' => CheckRole::class,
            'permission' => CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            if ($request->is($apiRoutePattern)) {
                return response()->json(['message' => 'Ressource non trouvée'], 404);
            }
        });

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is($apiRoutePattern)) {
                return response()->json(['message' => 'Non authentifié'], 401);
            }
        });

        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->is($apiRoutePattern)) {
                return response()->json(['message' => 'Action non autorisée'], 403);
            }
        });

        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->is($apiRoutePattern)) {
                return response()->json([
                    'message' => 'Données invalides',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (\Throwable $e, $request) {
            if (! config('app.debug')) {
                if ($request->is($apiRoutePattern)) {
                    return response()->json(['message' => 'Une erreur interne est survenue'], 500);
                }
                // Pour les requêtes Web, Laravel utilisera automatiquement resources/views/errors/500.blade.php
            }
        });
    })->create();
