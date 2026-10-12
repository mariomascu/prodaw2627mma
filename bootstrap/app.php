<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Vercel sirve la app detrás de su proxy: así las URLs se generan con https.
        $middleware->trustProxies(at: '*');

        // Idioma de cada página (selector de idioma)
        $middleware->web(append: [
            \App\Http\Middleware\EstablecerIdioma::class,
        ]);

        $middleware->alias([
            'admin'  => \App\Http\Middleware\EsAdmin::class,
            'perfil' => \App\Http\Middleware\PerfilCompleto::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
