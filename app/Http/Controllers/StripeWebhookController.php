<?php

namespace App\Http\Controllers;

use App\Services\Pagos;
use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

/**
 * Webhook de Stripe: Stripe llama a POST /stripe/webhook cada vez que pasa algo con un pago
 * (pagado, caducado…). Así el pago se confirma aunque el cliente cierre la pestaña al pagar.
 */
class StripeWebhookController extends Controller
{
    public function __invoke(Request $request, Pagos $pagos)
    {
        $secreto = config('services.stripe.webhook_secret');
        abort_unless(filled($secreto), 404);

        // Se comprueba la firma: solo Stripe conoce el secreto, así nadie puede enviar avisos falsos
        try {
            $evento = Webhook::constructEvent(
                $request->getContent(),
                (string) $request->header('Stripe-Signature'),
                $secreto
            );
        } catch (UnexpectedValueException|SignatureVerificationException) {
            return response('Firma no válida', 400);
        }

        $pagos->procesarEvento($evento);

        // Stripe solo necesita un 200; si no lo recibe, vuelve a intentarlo durante unos días
        return response('ok', 200);
    }
}
