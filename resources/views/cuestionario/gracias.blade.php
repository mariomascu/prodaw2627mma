@extends('layouts.yoguifit')
@section('title', 'Solicitud enviada — YoguiFit')

@section('content')
<div style="max-width:560px;margin:3rem auto;text-align:center;padding:0 1.25rem">
    <div style="background:#fff;border-radius:16px;padding:3rem 1.5rem;box-shadow:0 4px 20px rgba(52,130,137,.12)">
        <div style="width:80px;height:80px;border-radius:50%;background:var(--selected-bg);display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem">
            <i class="bi bi-check-circle-fill" style="font-size:3rem;color:var(--primary)"></i>
        </div>
        <h1 style="font-family:'Montez',cursive;font-size:2.2rem;color:var(--primary-dark);margin-bottom:.75rem">
            ¡Solicitud enviada!
        </h1>
        <p style="color:#666;line-height:1.7;margin-bottom:2rem">
            Hemos recibido tu solicitud de reserva. Nos pondremos en contacto contigo
            a la brevedad posible para confirmar tu cita.
        </p>
        <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap">
            <a href="{{ route('home') }}" class="btn-outline-yf">
                <i class="bi bi-house"></i> Volver al inicio
            </a>
            <a href="{{ route('servicios') }}" class="btn-primary-yf">
                <i class="bi bi-grid-3x3-gap"></i> Ver más servicios
            </a>
        </div>
    </div>
</div>
@endsection
