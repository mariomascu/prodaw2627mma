<?php

namespace App\Services;

use App\Models\Ajuste;
use App\Models\Bono;
use App\Models\Cita;
use App\Models\Franja;
use App\Models\Ocupacion;
use App\Models\Pago;
use App\Models\Servicio;
use App\Models\SolicitudCambio;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Lógica de la agenda de citas:
 * - el administrador abre los meses (se crean las franjas de una hora según el horario);
 * - el cliente reserva las horas libres, pide cambios o cancela;
 * - el administrador aprueba o rechaza los cambios.
 */
class Agenda
{
    public function __construct(private Pagos $pagos)
    {
    }

    /**
     * Abre un mes: crea una franja por cada hora en la que cabe una sesión de 60 minutos
     * dentro del horario (p. ej. de 10:00 a 14:00 → 10, 11, 12 y 13 h). No crea horas pasadas.
     * Devuelve cuántas franjas nuevas se han creado.
     */
    public function abrirMes(Carbon $mes): int
    {
        $horario = Ajuste::horario();
        $creadas = 0;

        $dia = $mes->copy()->startOfMonth();
        $fin = $mes->copy()->endOfMonth();

        for (; $dia->lte($fin); $dia->addDay()) {
            foreach ($horario[$dia->dayOfWeekIso] as [$apertura, $cierre]) {
                $hora = $dia->copy()->setTimeFromTimeString($apertura);
                $limite = $dia->copy()->setTimeFromTimeString($cierre);

                for (; $hora->copy()->addHour()->lte($limite); $hora->addHour()) {
                    if ($hora->isPast()) {
                        continue;
                    }
                    $franja = Franja::firstOrCreate(['inicio' => $hora->copy()]);
                    $creadas += $franja->wasRecentlyCreated ? 1 : 0;
                }
            }
        }

        return $creadas;
    }

    /** Quita del mes las franjas futuras que nadie ha reservado (las ocupadas se quedan). */
    public function cerrarHuecosLibres(Carbon $mes): int
    {
        return Franja::whereBetween('inicio', [$mes->copy()->startOfMonth(), $mes->copy()->endOfMonth()])
            ->where('inicio', '>', now())
            ->whereDoesntHave('ocupacion')
            ->delete();
    }

    /**
     * Horas de inicio disponibles para un servicio: ['2026-11-03' => ['10:00', '16:00', …], …].
     * Una hora está disponible si hay tantas franjas seguidas libres como horas necesita el servicio.
     */
    public function horasDisponibles(Servicio $servicio): array
    {
        $this->liberarPagosCaducados();

        $libres = Franja::where('inicio', '>', now())
            ->where('bloqueada', false)
            ->whereDoesntHave('ocupacion')
            ->orderBy('inicio')
            ->pluck('inicio');

        // Para buscar rápido si una hora está libre
        $estaLibre = [];
        foreach ($libres as $inicio) {
            $estaLibre[$inicio->format('Y-m-d H:i')] = true;
        }

        $disponibles = [];
        foreach ($libres as $inicio) {
            if ($this->hayHorasSeguidas($inicio, $servicio->horasNecesarias(), $estaLibre)) {
                $disponibles[$inicio->format('Y-m-d')][] = $inicio->format('H:i');
            }
        }

        return $disponibles;
    }

    /**
     * Reserva una sesión de masaje o yoga.
     * - Con Stripe configurado, la cita queda «pendiente» (con la hora apartada) y se crea un pago pendiente;
     *   al pagar pasa a «confirmada». Sin Stripe, se confirma directamente.
     * - Yoga: si el cliente tiene un bono con sesiones libres, la sesión va contra el bono y no se cobra;
     *   si no, se cobra el pack completo y se crea un bono nuevo con sus sesiones.
     */
    public function reservar(User $user, Servicio $servicio, Carbon $inicio, string $telefono, ?string $notas = null): Cita
    {
        $this->liberarPagosCaducados();

        // El descuento depende de las sesiones que el cliente tenía ANTES de esta reserva
        $descuento = $user->nivel()['descuento'] ?? 0;
        $bonoExistente = $servicio->tipo === 'yoga' ? $user->bonoDisponible($servicio) : null;

        return $this->conHorasLibres(function () use ($user, $servicio, $inicio, $telefono, $notas, $descuento, $bonoExistente) {
            $franjas = $this->franjasLibres($inicio, $servicio->horasNecesarias());
            $cobrar = Pagos::configurado() && ! $bonoExistente;
            $bono = $bonoExistente;

            // Yoga sin bono: se crea el bono del pack (activo al pagarlo)
            if ($servicio->tipo === 'yoga' && ! $bono) {
                $bono = Bono::create([
                    'user_id'          => $user->id,
                    'servicio_id'      => $servicio->id,
                    'sesiones_totales' => $servicio->sesionesPack(),
                    'estado'           => $cobrar ? 'pendiente' : 'activo',
                ]);
            }

            $precio = $bonoExistente ? null : Servicio::aplicarDescuento($servicio->precio, $descuento);

            $cita = Cita::create([
                'user_id'      => $user->id,
                'servicio_id'  => $servicio->id,
                'bono_id'      => $bono?->id,
                'nombre'       => $user->name,
                'telefono'     => $telefono,
                'notas'        => $notas,
                'estado'       => $cobrar ? 'pendiente' : 'confirmada',
                'inicio'       => $inicio,
                'fin'          => $inicio->copy()->addMinutes($servicio->minutosSesion()),
                'descuento'    => ($bonoExistente || ! $descuento) ? null : $descuento,
                'precio_final' => $precio,
            ]);

            foreach ($franjas as $franja) {
                Ocupacion::create(['franja_id' => $franja->id, 'cita_id' => $cita->id]);
            }

            if ($cobrar) {
                Pago::create([
                    'user_id'  => $user->id,
                    'cita_id'  => $cita->id,
                    'bono_id'  => $servicio->tipo === 'yoga' ? $bono->id : null,
                    'concepto' => $servicio->tipo === 'yoga'
                        ? $servicio->titulo . ' (' . $servicio->duracion . ')'
                        : $servicio->titulo . ' · ' . $inicio->format('d/m/Y H:i'),
                    'importe'  => Servicio::importeEnCentimos($precio),
                ]);
            }

            return $cita;
        });
    }

    /** El cliente pide mover su cita. Las horas nuevas quedan apartadas hasta que el admin decida. */
    public function solicitarCambio(Cita $cita, Carbon $inicio): SolicitudCambio
    {
        return $this->conHorasLibres(function () use ($cita, $inicio) {
            $franjas = $this->franjasLibres($inicio, $cita->servicio->horasNecesarias());

            $solicitud = SolicitudCambio::create([
                'cita_id' => $cita->id,
                'inicio'  => $inicio,
                'fin'     => $inicio->copy()->addMinutes($cita->servicio->minutosSesion()),
            ]);

            foreach ($franjas as $franja) {
                Ocupacion::create(['franja_id' => $franja->id, 'cita_id' => $cita->id, 'solicitud_cambio_id' => $solicitud->id]);
            }

            return $solicitud;
        });
    }

    /** El admin aprueba el cambio: la cita pasa a las horas nuevas y se liberan las antiguas. */
    public function aprobarCambio(SolicitudCambio $solicitud): void
    {
        DB::transaction(function () use ($solicitud) {
            $cita = $solicitud->cita;

            $cita->ocupaciones()->whereNull('solicitud_cambio_id')->delete();
            Ocupacion::where('solicitud_cambio_id', $solicitud->id)->update(['solicitud_cambio_id' => null]);

            $cita->update(['inicio' => $solicitud->inicio, 'fin' => $solicitud->fin]);
            $solicitud->update(['estado' => 'aprobada']);
        });
    }

    /** El admin rechaza el cambio: se liberan las horas apartadas y la cita sigue como estaba. */
    public function rechazarCambio(SolicitudCambio $solicitud): void
    {
        DB::transaction(function () use ($solicitud) {
            Ocupacion::where('solicitud_cambio_id', $solicitud->id)->delete();
            $solicitud->update(['estado' => 'rechazada']);
        });
    }

    /**
     * Cancela una cita (cliente con 24 h de antelación o admin) y libera sus horas.
     * Si estaba pagada, se devuelve el dinero con Stripe. Las sesiones de yoga no se devuelven:
     * la sesión vuelve al bono. Devuelve null si no había nada que devolver, true si se ha
     * devuelto y false si Stripe no ha podido hacer la devolución.
     */
    public function cancelar(Cita $cita): ?bool
    {
        $pago = $cita->pago;
        $reembolso = null;

        if ($pago && $pago->estado === 'pagado' && ! $pago->bono_id) {
            $reembolso = $this->pagos->reembolsar($pago);
        }

        DB::transaction(function () use ($cita, $pago) {
            $cita->ocupaciones()->delete();
            $cita->solicitudes()->where('estado', 'pendiente')->update(['estado' => 'rechazada']);
            $cita->update(['estado' => 'cancelada']);

            // Si aún no se había pagado, se anula el pago (y el bono que se iba a comprar)
            if ($pago && $pago->estado === 'pendiente') {
                $pago->update(['estado' => 'cancelado']);
                if ($pago->bono && $pago->bono->estado === 'pendiente') {
                    $pago->bono->update(['estado' => 'cancelado']);
                }
            }
        });

        return $reembolso;
    }

    /**
     * Pagos abandonados: si el cliente no paga en el tiempo previsto, se anula la reserva
     * y la hora vuelve a quedar libre. Se ejecuta al consultar la agenda.
     */
    public function liberarPagosCaducados(): void
    {
        Pago::with('cita.servicio', 'cita.pago')
            ->where('estado', 'pendiente')
            ->where('updated_at', '<', now()->subMinutes(Pagos::MINUTOS_PARA_PAGAR + 5))
            ->get()
            ->each(function (Pago $pago) {
                if ($pago->cita && $pago->cita->estado === 'pendiente' && $pago->cita->servicio?->usaAgenda()) {
                    $this->cancelar($pago->cita);
                } else {
                    $pago->update(['estado' => 'cancelado']);
                }
            });
    }

    // ──────────────────────────────────────────────────────────────

    /** ¿Hay $horas franjas libres seguidas empezando en $inicio? */
    private function hayHorasSeguidas(Carbon $inicio, int $horas, array $estaLibre): bool
    {
        for ($i = 0; $i < $horas; $i++) {
            if (! isset($estaLibre[$inicio->copy()->addHours($i)->format('Y-m-d H:i')])) {
                return false;
            }
        }

        return true;
    }

    /** Devuelve las franjas libres necesarias o lanza un error de validación si alguna no lo está. */
    private function franjasLibres(Carbon $inicio, int $horas)
    {
        $franjas = collect();

        for ($i = 0; $i < $horas; $i++) {
            $franja = Franja::where('inicio', $inicio->copy()->addHours($i))
                ->where('bloqueada', false)
                ->whereDoesntHave('ocupacion')
                ->first();

            if (! $franja || $inicio->isPast()) {
                throw ValidationException::withMessages([
                    'inicio' => __('Esa hora ya no está disponible. Elige otra, por favor.'),
                ]);
            }

            $franjas->push($franja);
        }

        return $franjas;
    }

    /**
     * Ejecuta la operación en una transacción. Si otro cliente ha reservado la misma hora
     * a la vez, la base de datos lo impide (franja_id es único) y se muestra un error amable.
     */
    private function conHorasLibres(callable $operacion)
    {
        try {
            return DB::transaction($operacion);
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'unique') || str_contains($e->getMessage(), 'UNIQUE')) {
                throw ValidationException::withMessages([
                    'inicio' => __('Esa hora ya no está disponible. Elige otra, por favor.'),
                ]);
            }
            throw $e;
        }
    }
}
