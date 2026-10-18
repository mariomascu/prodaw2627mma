<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Services\Agenda;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CitaController extends Controller
{
    public function index(Request $request, Agenda $agenda)
    {
        $agenda->liberarPagosCaducados();
        $user = $request->user();

        $citas = Cita::with(['servicio.traducciones', 'solicitudPendiente', 'pago', 'bono'])
            ->where('user_id', $user->id)
            ->orderByRaw('inicio is null')      // primero las que tienen fecha
            ->orderByDesc('inicio')
            ->orderByDesc('created_at')
            ->get();

        $sesiones = $user->sesionesReservadas();

        return view('citas.index', [
            'citas'     => $citas,
            'bonos'     => $user->bonos()->with('servicio.traducciones')->where('estado', 'activo')->latest()->get(),
            'sesiones'  => $sesiones,
            'nivel'     => $user->nivel($sesiones),
            'siguiente' => $user->siguienteNivel($sesiones),
        ]);
    }

    /** El cliente pide cambiar su cita a otra hora libre (lo aprueba el administrador). */
    public function solicitarCambio(Request $request, Cita $cita, Agenda $agenda)
    {
        abort_unless($cita->user_id === $request->user()->id, 403);

        if (! $cita->sePuedeCambiar()) {
            return back()->with('error', __('Esta cita ya no se puede cambiar.'));
        }

        $datos = $request->validate([
            'inicio' => 'required|date_format:Y-m-d H:i',
        ], [
            'inicio.required' => __('Elige un día y una hora.'),
        ]);

        $agenda->solicitarCambio($cita, Carbon::createFromFormat('Y-m-d H:i', $datos['inicio']));

        return back()->with('success', __('Hemos enviado tu solicitud de cambio. Te avisaremos cuando la revisemos.'));
    }

    /** El cliente cancela su cita (solo con 24 horas de antelación). */
    public function cancelar(Request $request, Cita $cita, Agenda $agenda)
    {
        abort_unless($cita->user_id === $request->user()->id, 403);

        if (! $cita->sePuedeCancelar()) {
            return back()->with('error', __('Solo puedes cancelar una cita con al menos 24 horas de antelación. Llámanos si lo necesitas.'));
        }

        $reembolso = $agenda->cancelar($cita);

        return back()->with('success', match ($reembolso) {
            true    => __('Cita cancelada. Te hemos devuelto el importe.'),
            false   => __('Cita cancelada. No hemos podido hacer la devolución automática; contacta con nosotros.'),
            default => __('Cita cancelada.'),
        });
    }
}
