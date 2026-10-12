<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Franja;
use App\Models\SolicitudCambio;
use App\Services\Agenda;
use Carbon\Carbon;
use Illuminate\Http\Request;

/** Agenda del administrador: abrir meses, bloquear horas y aprobar cambios de cita. */
class AgendaController extends Controller
{
    public function index(Request $request)
    {
        $mes = $this->mes($request);

        // Franjas del mes con quién las ocupa, agrupadas por día
        $franjas = Franja::with('ocupacion.cita.user', 'ocupacion.cita.servicio')
            ->whereBetween('inicio', [$mes->copy()->startOfMonth(), $mes->copy()->endOfMonth()])
            ->orderBy('inicio')
            ->get()
            ->groupBy(fn ($f) => $f->inicio->format('Y-m-d'));

        $solicitudes = SolicitudCambio::with('cita.user', 'cita.servicio')
            ->where('estado', 'pendiente')
            ->orderBy('created_at')
            ->get();

        return view('admin.agenda', compact('mes', 'franjas', 'solicitudes'));
    }

    public function abrir(Request $request, Agenda $agenda)
    {
        $mes = $this->mes($request);
        $creadas = $agenda->abrirMes($mes);

        return redirect()->route('admin.agenda', ['mes' => $mes->format('Y-m')])
            ->with('success', __('Mes abierto: :numero horas disponibles nuevas.', ['numero' => $creadas]));
    }

    public function cerrar(Request $request, Agenda $agenda)
    {
        $mes = $this->mes($request);
        $borradas = $agenda->cerrarHuecosLibres($mes);

        return redirect()->route('admin.agenda', ['mes' => $mes->format('Y-m')])
            ->with('success', __('Se han quitado :numero horas libres. Las reservas se mantienen.', ['numero' => $borradas]));
    }

    /** Bloquea o desbloquea una hora libre (festivos, vacaciones…). */
    public function bloquear(Franja $franja)
    {
        if ($franja->ocupacion) {
            return back()->with('error', __('Esa hora está reservada. Cancela antes la cita.'));
        }

        $franja->update(['bloqueada' => ! $franja->bloqueada]);

        return back();
    }

    public function aprobar(SolicitudCambio $solicitud, Agenda $agenda)
    {
        abort_unless($solicitud->estado === 'pendiente', 404);
        $agenda->aprobarCambio($solicitud);

        return back()->with('success', __('Cambio de cita aprobado.'));
    }

    public function rechazar(SolicitudCambio $solicitud, Agenda $agenda)
    {
        abort_unless($solicitud->estado === 'pendiente', 404);
        $agenda->rechazarCambio($solicitud);

        return back()->with('success', __('Cambio de cita rechazado. La cita se mantiene.'));
    }

    /** Mes elegido (?mes=2026-11) o el actual. */
    private function mes(Request $request): Carbon
    {
        $texto = (string) $request->input('mes');

        return preg_match('/^\d{4}-\d{2}$/', $texto)
            ? Carbon::createFromFormat('Y-m-d', $texto . '-01')->startOfDay()
            : now()->startOfMonth();
    }
}
