<?php

namespace App\Services;

use App\Mail\BienvenidaMail;
use App\Mail\ReservaConfirmadaMail;
use App\Models\Cita;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Envío de los correos a los clientes (con Brevo, configurado en MAIL_* del .env y de Vercel).
 * - Se envían en el momento (sin cola): en Vercel no hay ningún proceso que vacíe la cola.
 * - Si el envío falla, se registra el error en el log pero la reserva o el registro siguen adelante.
 * - Cada correo sale en el idioma del cliente (User::preferredLocale()).
 */
class Correos
{
    public static function bienvenida(User $user): void
    {
        self::enviar(fn () => Mail::to($user)->send(new BienvenidaMail($user)));
    }

    /** Cita confirmada (o cambiada): datos de la reserva y código QR. */
    public static function reservaConfirmada(Cita $cita, bool $cambio = false): void
    {
        if (! $cita->user || ! $cita->inicio || $cita->estado !== 'confirmada') {
            return;
        }

        self::enviar(fn () => Mail::to($cita->user)->send(new ReservaConfirmadaMail($cita, $cambio)));
    }

    private static function enviar(callable $envio): void
    {
        try {
            $envio();
        } catch (Throwable $e) {
            report($e);
        }
    }
}
