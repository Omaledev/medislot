<?php

use App\Http\Middleware\AuthenticateHospitalApiKey;
use App\Http\Middleware\EnsureSuperAdmin;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\ResolveHospitalTenant;
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
        $middleware->web(append: [
            ResolveHospitalTenant::class,
        ]);

        $middleware->alias([
            'role' => EnsureUserHasRole::class,
            'superadmin' => EnsureSuperAdmin::class,
            'hospital.api_key' => AuthenticateHospitalApiKey::class,
            'tenant' => ResolveHospitalTenant::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
