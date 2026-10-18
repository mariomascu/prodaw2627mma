<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Correo de bienvenida al registrarse (también con Google). */
class BienvenidaMail extends Mailable
{
    public function __construct(public User $user)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('¡Bienvenida/o a YoguiFit, :nombre!', ['nombre' => $this->user->name]));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.bienvenida');
    }
}
