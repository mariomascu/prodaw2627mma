<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Pago;
use App\Models\Servicio;
use App\Services\Agenda;
use App\Services\Pagos;
use Illuminate\Http\Request;

/** Vuelta de Stripe (pago hecho o cancelado) y botón «Pagar» de «Mis citas». */
class PagoController extends Controller
{
    /** Stripe devuelve aquí al cliente tras pagar. */
    public function exito(Request $request, Pago $pago, Pagos $pagos)
    {
        abort_unless($pago->user_id === $request->user()->id, 403);

        if (! $pagos->confirmar($pago, (string) $request->query('session_id'))) {
            return redirect()->route('citas')->with('error', __('No hemos podido confirmar el pago. Si se ha cobrado, contacta con nosotros.'));
        }

        return redirect()->route('citas')->with('success', __('¡Pago completado! Tu reserva está confirmada.'));
    }

    /** El cliente ha cancelado en la página de Stripe: se libera la hora apartada. */
    public function cancelado(Request $request, Pago $pago, Agenda $agenda)
    {
        abort_unless($pago->user_id === $request->user()->id, 403);

        if ($pago->estado === 'pendiente') {
            $cita = $pago->cita;
            if ($cita && $cita->estado === 'pendiente' && $cita->servicio?->usaAgenda()) {
                $agenda->cancelar($cita);   // masaje o yoga: se libera la hora
            } else {
                $pago->update(['estado' => 'cancelado']); // curso: sigue aceptado, podrá pagarlo más tarde
            }
        }

        return redirect()->route('citas')->with('error', __('Has cancelado el pago. La reserva no se ha completado.'));
    }

    /** Botón «Pagar» de «Mis citas»: curso aceptado o reserva cuyo pago quedó a medias. */
    public function pagar(Request $request, Cita $cita, Pagos $pagos)
    {
        abort_unless($cita->user_id === $request->user()->id, 403);
        abort_unless(Pagos::configurado(), 404);

        if ($cita->esperandoPago()) {
            $pago = $cita->pago;                       // se retoma el mismo pago
        } elseif ($cita->cursoPorPagar()) {
            $pago = Pago::create([
                'user_id'  => $cita->user_id,
                'cita_id'  => $cita->id,
                'concepto' => $cita->servicio->titulo,
                'importe'  => Servicio::importeEnCentimos($cita->precio_final ?? $cita->servicio->precio),
            ]);
        } else {
            return back()->with('error', __('Esta cita no tiene nada pendiente de pago.'));
        }

        return redirect()->away($pagos->urlDePago($pago, $request->user()->email));
    }
}
