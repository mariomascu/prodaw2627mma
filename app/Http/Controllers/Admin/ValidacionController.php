<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cita;

/** Lector de QR del administrador: muestra la cita del cliente y la da por realizada. */
class ValidacionController extends Controller
{
    /** Página con la cámara para leer el QR. */
    public function escaner()
    {
        return view('admin.escaner');
    }

    /** Al leer el QR se abre esta página con los datos de la cita y de la clienta. */
    public function mostrar(Cita $cita, string $codigo)
    {
        abort_unless(hash_equals((string) $cita->codigo, $codigo), 404);

        $cita->load('user', 'servicio');
        $sesiones = $cita->user?->sesionesReservadas() ?? 0;

        return view('admin.validar', [
            'cita'     => $cita,
            'codigo'   => $codigo,
            'nivel'    => $cita->user?->nivel($sesiones),
            'sesiones' => $sesiones,
        ]);
    }

    /** La masajista confirma que la sesión se ha realizado. */
    public function validar(Cita $cita, string $codigo)
    {
        abort_unless(hash_equals((string) $cita->codigo, $codigo), 404);

        if ($cita->estado === 'cancelada') {
            return back()->with('error', __('Esta cita está cancelada y no se puede validar.'));
        }
        if ($cita->validada_at) {
            return back()->with('error', __('Esta sesión ya se validó el :fecha.', ['fecha' => $cita->validada_at->format('d/m/Y H:i')]));
        }

        $cita->update(['validada_at' => now(), 'estado' => 'confirmada']);

        return back()->with('success', __('Sesión validada como realizada.'));
    }
}
