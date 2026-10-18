<?php

namespace App\Services;

use App\Models\Pago;
use Illuminate\Support\Facades\DB;
use Stripe\StripeClient;
use Throwable;

/**
 * Cobros con Stripe Checkout (la página de pago la muestra Stripe: los datos de la tarjeta
 * nunca pasan por nuestra web). Flujo:
 *   1) urlDePago()  → crea la sesión de pago en Stripe y devuelve su dirección.
 *   2) El cliente paga en Stripe. El pago se confirma por dos caminos (el que llegue antes):
 *      a) webhook: Stripe avisa a /stripe/webhook en cuanto termina el pago → procesarEvento();
 *      b) el cliente vuelve a /pagos/{pago}/exito → confirmar().
 *      Los dos acaban en marcarPagado(), que no hace nada si el pago ya estaba confirmado.
 *   3) reembolsar() → devolución al cancelar.
 * Las llamadas a Stripe están en los métodos stripe*() para poder simularlas en los tests.
 */
class Pagos
{
    /** Minutos que se aparta la hora mientras el cliente paga (Stripe exige al menos 30). */
    public const MINUTOS_PARA_PAGAR = 30;

    public static function configurado(): bool
    {
        return filled(config('services.stripe.secret'));
    }

    /** Crea la sesión de pago en Stripe y devuelve la dirección a la que se envía al cliente. */
    public function urlDePago(Pago $pago, string $email): string
    {
        $idiomas = ['es' => 'es', 'en' => 'en', 'de' => 'de', 'pt_BR' => 'pt-BR'];

        $sesion = $this->stripeCrearSesion([
            'mode' => 'payment',
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $pago->importe,
                    'product_data' => ['name' => $pago->concepto],
                ],
            ]],
            'customer_email' => $email,
            'client_reference_id' => (string) $pago->id,
            'metadata' => ['pago_id' => $pago->id],
            'locale' => $idiomas[app()->getLocale()] ?? 'auto',
            // Stripe sustituye {CHECKOUT_SESSION_ID} por el identificador de la sesión
            'success_url' => route('pagos.exito', $pago) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('pagos.cancelado', $pago),
            'expires_at' => now()->addMinutes(self::MINUTOS_PARA_PAGAR + 1)->timestamp,
        ]);

        $pago->update(['stripe_session_id' => $sesion->id]);

        return $sesion->url;
    }

    /**
     * El cliente vuelve de Stripe: se comprueba con Stripe que el pago está hecho
     * (no basta con que llegue a la página de éxito) y se confirma la cita o el bono.
     */
    public function confirmar(Pago $pago, string $sessionId): bool
    {
        if ($pago->estado === 'pagado') {
            return true;   // ya lo confirmó el webhook
        }
        if ($pago->stripe_session_id !== $sessionId) {
            return false;
        }

        $sesion = $this->stripeLeerSesion($sessionId);
        if (($sesion->payment_status ?? null) !== 'paid') {
            return false;
        }

        return $this->marcarPagado($pago, $this->idPaymentIntent($sesion));
    }

    /**
     * Aviso de Stripe (webhook) ya verificado con la firma. Tipos que se atienden:
     * - checkout.session.completed / async_payment_succeeded → el cliente ha pagado: se confirma.
     * - checkout.session.expired → el cliente no pagó a tiempo: se libera la hora.
     */
    public function procesarEvento(object $evento): void
    {
        $sesion = $evento->data->object;
        $pago = Pago::find($sesion->metadata->pago_id ?? null)
            ?? Pago::where('stripe_session_id', $sesion->id)->first();

        if (! $pago) {
            return;   // no es un pago de esta web
        }

        switch ($evento->type) {
            case 'checkout.session.completed':
            case 'checkout.session.async_payment_succeeded':
                if (($sesion->payment_status ?? null) === 'paid') {
                    $this->marcarPagado($pago, $this->idPaymentIntent($sesion));
                }
                break;

            case 'checkout.session.expired':
                // Solo si es la sesión vigente (al pulsar «Completar pago» se crea otra nueva)
                if ($pago->estado === 'pendiente' && $pago->stripe_session_id === $sesion->id) {
                    $cita = $pago->cita;
                    if ($cita && $cita->estado === 'pendiente' && $cita->servicio?->usaAgenda()) {
                        app(Agenda::class)->cancelar($cita);   // masaje o yoga: se libera la hora
                    } else {
                        $pago->update(['estado' => 'cancelado']);
                    }
                }
                break;
        }
    }

    /**
     * Marca un pago como pagado y confirma su cita o su bono. Se puede llamar varias veces
     * (webhook y vuelta del cliente): si ya estaba pagado no hace nada.
     * Si la reserva ya se había anulado (el cliente tardó demasiado), se devuelve el dinero.
     */
    public function marcarPagado(Pago $pago, ?string $paymentIntent): bool
    {
        $pago->refresh();

        if ($pago->estado === 'pagado') {
            return true;
        }

        if ($pago->estado !== 'pendiente') {
            // La hora ya se había liberado: no se puede confirmar, así que se devuelve el cobro
            $pago->update(['estado' => 'pagado', 'pagado_at' => now(), 'stripe_payment_intent' => $paymentIntent]);
            $this->reembolsar($pago);

            return false;
        }

        $citaConfirmada = DB::transaction(function () use ($pago, $paymentIntent) {
            $pago->update(['estado' => 'pagado', 'pagado_at' => now(), 'stripe_payment_intent' => $paymentIntent]);
            $confirmada = false;

            // Masaje o primera sesión de yoga: la cita que esperaba el pago queda confirmada
            if ($pago->cita && $pago->cita->estado === 'pendiente') {
                $pago->cita->update(['estado' => 'confirmada']);
                $confirmada = true;
            }
            // Pack de yoga: el bono pasa a estar activo
            if ($pago->bono && $pago->bono->estado === 'pendiente') {
                $pago->bono->update(['estado' => 'activo']);
            }

            return $confirmada;
        });

        // Correo con los datos de la reserva y el QR (solo cuando el pago confirma la cita)
        if ($citaConfirmada) {
            Correos::reservaConfirmada($pago->cita->fresh());
        }

        return true;
    }

    private function idPaymentIntent(object $sesion): ?string
    {
        $pi = $sesion->payment_intent ?? null;

        return is_string($pi) ? $pi : ($pi->id ?? null);
    }

    /** Devuelve un pago. Devuelve false si Stripe no ha podido hacer la devolución. */
    public function reembolsar(Pago $pago): bool
    {
        if ($pago->estado !== 'pagado' || ! $pago->stripe_payment_intent) {
            return false;
        }

        try {
            $this->stripeReembolsar($pago->stripe_payment_intent);
        } catch (Throwable $e) {
            report($e);

            return false;
        }

        $pago->update(['estado' => 'reembolsado', 'reembolsado_at' => now()]);

        return true;
    }

    // ── Llamadas a Stripe ──────────────────────────────────────────

    protected function stripe(): StripeClient
    {
        return new StripeClient(config('services.stripe.secret'));
    }

    protected function stripeCrearSesion(array $datos): object
    {
        return $this->stripe()->checkout->sessions->create($datos);
    }

    protected function stripeLeerSesion(string $id): object
    {
        return $this->stripe()->checkout->sessions->retrieve($id);
    }

    protected function stripeReembolsar(string $paymentIntent): void
    {
        $this->stripe()->refunds->create(['payment_intent' => $paymentIntent]);
    }
}
