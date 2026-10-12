<?php

namespace App\Providers;

use App\Database\NeonPostgresConnection;
use App\Models\Version;
use Illuminate\Support\Facades\View;
use Illuminate\Database\Connection;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Las conexiones pgsql usan nuestra clase, que envía bien los booleanos a Neon
        Connection::resolverFor('pgsql', function ($pdo, $database, $prefix, $config) {
            return new NeonPostgresConnection($pdo, $database, $prefix, $config);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Versión actual de la aplicación, disponible en las plantillas como $versionApp
        View::composer(['layouts.yoguifit', 'layouts.app'], function ($view) {
            $view->with('versionApp', Version::actual()?->numero);
        });
    }
}
