<?php

namespace App\Http\Middleware;

use App\Models\Ajuste;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Aplica el idioma en cada página. Orden de preferencia:
 * 1) el elegido en el selector (sesión), 2) el guardado en el perfil del usuario,
 * 3) el idioma por defecto del panel de configuración.
 */
class EstablecerIdioma
{
    public function handle(Request $request, Closure $next): Response
    {
        Ajuste::olvidar(); // los ajustes se leen de nuevo en cada petición
        $activos = Ajuste::idiomasActivos();
        $porDefecto = Ajuste::valor('idioma_por_defecto');

        $idioma = $request->session()->get('idioma')
            ?? $request->user()?->idioma
            ?? $porDefecto;

        if (! array_key_exists($idioma, $activos)) {
            $idioma = array_key_exists($porDefecto, Ajuste::IDIOMAS) ? $porDefecto : 'es';
        }

        app()->setLocale($idioma);
        Carbon::setLocale($idioma);

        return $next($request);
    }
}
