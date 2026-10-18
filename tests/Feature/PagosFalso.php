<?php

namespace Tests\Feature;

use App\Services\Pagos;

/** Pasarela simulada: imita las respuestas de Stripe sin conectarse a internet. */
class PagosFalso extends Pagos
{
    public array $sesiones = [];
    public array $reembolsos = [];
    public string $estadoPago = 'paid';
    public bool $fallarReembolso = false;

    protected function stripeCrearSesion(array $datos): object
    {
        $this->sesiones[] = $datos;
        $id = 'cs_test_' . count($this->sesiones);

        return (object) ['id' => $id, 'url' => 'https://checkout.stripe.com/c/pay/' . $id];
    }

    protected function stripeLeerSesion(string $id): object
    {
        return (object) ['payment_status' => $this->estadoPago, 'payment_intent' => 'pi_' . $id];
    }

    protected function stripeReembolsar(string $paymentIntent): void
    {
        if ($this->fallarReembolso) {
            throw new \RuntimeException('Stripe no disponible');
        }
        $this->reembolsos[] = $paymentIntent;
    }
}
