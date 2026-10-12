<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Cuestionario que el cliente responde una sola vez, al registrarse.
 * Las respuestas se guardan en la tabla users para las estadísticas del panel de administración.
 */
class CuestionarioController extends Controller
{
    public function index(Request $request)
    {
        // Si ya lo respondió, no se vuelve a preguntar
        if ($request->user()->perfilCompleto() || $request->user()->is_admin) {
            return redirect()->intended(route('servicios'));
        }

        return view('cuestionario.index', [
            'sexos'       => User::SEXOS,
            'objetivos'   => User::OBJETIVOS,
            'frecuencias' => User::FRECUENCIAS,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->perfilCompleto()) {
            return redirect()->intended(route('servicios'));
        }

        $datos = $request->validate([
            'sexo'       => ['required', Rule::in(array_keys(User::SEXOS))],
            'objetivo'   => ['required', Rule::in(array_keys(User::OBJETIVOS))],
            'frecuencia' => ['required', Rule::in(array_keys(User::FRECUENCIAS))],
            'telefono'   => ['required', 'string', 'max:20'],
        ], [
            'sexo.required'       => __('Selecciona tu sexo.'),
            'objetivo.required'   => __('Selecciona tu objetivo.'),
            'frecuencia.required' => __('Selecciona con qué frecuencia quieres venir.'),
            'telefono.required'   => __('Indica un teléfono de contacto.'),
        ]);

        $user->update($datos + ['perfil_completado_at' => now()]);

        return redirect()->intended(route('servicios'))
            ->with('success', __('¡Gracias! Ya puedes reservar tus servicios.'));
    }
}
