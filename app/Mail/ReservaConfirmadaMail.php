<?php

namespace App\Mail;

use App\Models\Cita;
use App\Services\CodigoQr;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Confirmación de una cita con todos sus datos y el código QR.
 * El QR va incrustado en el cuerpo del correo y, además, como adjunto para poder guardarlo.
 * $cambio = true cuando el administrador ha aprobado un cambio de día u hora.
 */
class ReservaConfirmadaMail extends Mailable
{
    public string $qrPng;

    public function __construct(public Cita $cita, public bool $cambio = false)
    {
        $this->cita->loadMissing('servicio.traducciones', 'pago', 'bono', 'user');
        $this->qrPng = CodigoQr::png($cita->urlValidacion());
    }

    public function envelope(): Envelope
    {
        $datos = [
            'servicio' => $this->cita->servicio?->t('titulo'),
            'fecha'    => $this->cita->inicio->locale(app()->getLocale())->translatedFormat('j M, H:i'),
        ];

        return new Envelope(subject: $this->cambio
            ? __('Tu cita ha cambiado: :servicio, :fecha', $datos)
            : __('Reserva confirmada: :servicio, :fecha', $datos));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.reserva');
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->qrPng, 'codigo-qr-yoguifit.png')->withMime('image/png'),
        ];
    }
}
