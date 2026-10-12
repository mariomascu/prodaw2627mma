<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Obliga a los clientes a responder el cuestionario del registro antes de reservar.
 * Guarda la página a la que iban para volver a ella al terminar.
 */
class PerfilCompleto
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->is_admin && ! $user->perfilCompleto()) {
            if ($request->isMethod('get')) {
                redirect()->setIntendedUrl($request->fullUrl());
            }

            return redirect()->route('cuestionario');
        }

        return $next($request);
    }
}
