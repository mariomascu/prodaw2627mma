<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Servicio;
use App\Services\Agenda;
use App\Services\Pagos;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    /**
     * Página de reserva de un servicio.
     * - Sin sesión: botones para registrarse o iniciar sesión.
     * - Con sesión pero sin cuestionario: se le manda a responderlo.
     * - Masajes y yoga: calendario con las horas libres. Cursos: formulario de solicitud.
     */
    public function create(Servicio $servicio)
    {
        if (! auth()->check()) {
            // Al registrarse o iniciar sesión volverá a esta misma página
            redirect()->setIntendedUrl(route('reservar', $servicio));

            return view('reservar.acceso', compact('servicio'));
        }

        $user = auth()->user();
        if (! $user->is_admin && ! $user->perfilCompleto()) {
            redirect()->setIntendedUrl(route('reservar', $servicio));

            return redirect()->route('cuestionario');
        }

        return view('reservar.create', compact('servicio', 'user'));
    }

    /** Horas libres de un servicio, en JSON, para el calendario. */
    public function disponibilidad(Servicio $servicio, Agenda $agenda)
    {
        abort_unless($servicio->usaAgenda(), 404);

        return response()->json($agenda->horasDisponibles($servicio));
    }

    public function store(Request $request, Servicio $servicio, Agenda $agenda, Pagos $pagos)
    {
        $user = $request->user();

        // Masajes y yoga: reserva directa de una hora libre (queda confirmada)
        if ($servicio->usaAgenda()) {
            $datos = $request->validate([
                'inicio'   => 'required|date_format:Y-m-d H:i',
                'telefono' => 'required|string|max:20',
                'notas'    => 'nullable|string|max:500',
            ], [
                'inicio.required' => __('Elige un día y una hora.'),
            ]);

            $cita = $agenda->reservar($user, $servicio, Carbon::createFromFormat('Y-m-d H:i', $datos['inicio']), $datos['telefono'], $datos['notas'] ?? null);

            // Hay que pagar: se envía al cliente a la página de pago de Stripe (la hora queda apartada)
            if ($cita->esperandoPago()) {
                return redirect()->away($pagos->urlDePago($cita->pago, $user->email));
            }

            // Sesión de yoga que va contra un bono que ya tenía (no lleva precio: no se cobra)
            if ($cita->bono_id && ! $cita->precio_final) {
                return redirect()->route('citas')->with('success', __('¡Sesión reservada con tu bono! Te quedan :numero sesiones por reservar.', [
                    'numero' => $cita->bono->sesionesLibres(),
                ]));
            }

            return redirect()->route('citas')->with('success', __('¡Reserva confirmada! «:titulo» el :fecha a las :hora.', [
                'titulo' => $servicio->t('titulo'),
                'fecha'  => $cita->inicio->translatedFormat('l j \d\e F'),
                'hora'   => $cita->inicio->format('H:i'),
            ]));
        }

        // Cursos: solicitud que confirma el administrador
        $datos = $request->validate([
            'telefono' => 'required|string|max:20',
            'notas'    => 'nullable|string|max:500',
        ]);

        $descuento = $user->nivel()['descuento'] ?? 0;

        Cita::create([
            'user_id'      => $user->id,
            'servicio_id'  => $servicio->id,
            'nombre'       => $user->name,
            'telefono'     => $datos['telefono'],
            'notas'        => $datos['notas'] ?? null,
            'estado'       => 'pendiente',
            'descuento'    => $descuento ?: null,
            'precio_final' => Servicio::aplicarDescuento($servicio->precio, $descuento),
        ]);

        return redirect()->route('citas')
            ->with('success', __('Hemos recibido tu solicitud para «:titulo». Te contactaremos para confirmar la cita.', ['titulo' => $servicio->t('titulo')]));
    }
}
