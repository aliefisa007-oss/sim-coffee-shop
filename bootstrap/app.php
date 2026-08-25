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
->withProviders([
    App\Providers\RepositoryServiceProvider::class,
])
    ->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role'   => \App\Http\Middleware\RoleMiddleware::class,
        'active' => \App\Http\Middleware\CheckActiveUser::class,
	'role.api' => \App\Http\Middleware\CheckRoleApi::class,
    ]);

    $middleware->web(append: [
        \App\Http\Middleware\NoCacheHtml::class,
    ]);
})
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
