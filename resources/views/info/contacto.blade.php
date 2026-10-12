@extends('layouts.yoguifit')
@section('title', __('Contacto') . ' — YoguiFit')
@section('content')
@php
    // Datos del centro (se cambian en Panel > Configuración > Datos del centro)
    $telefono  = \App\Models\Ajuste::valor('centro_telefono');
    $email     = \App\Models\Ajuste::valor('centro_email');
    $instagram = \App\Models\Ajuste::valor('centro_instagram');
@endphp
<div class="page-banner">
    <h1><i class="bi bi-telephone"></i> {{ __('Contacto') }}</h1>
    <p>{{ __('Estamos aquí para atenderte') }}</p>
</div>
<div class="container section" style="max-width:700px">
    <div class="card-yf card-pad">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(min(180px,100%),1fr));gap:2rem">
            <div style="text-align:center">
                <i class="bi bi-whatsapp" style="font-size:2.5rem;color:var(--primary);display:block;margin-bottom:.75rem"></i>
                <h3 style="color:var(--primary-dark);margin-bottom:.4rem">{{ __('Teléfono / WhatsApp') }}</h3>
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $telefono) }}" style="color:var(--primary);font-size:1.1rem;text-decoration:none">{{ $telefono }}</a>
            </div>
            <div style="text-align:center">
                <i class="bi bi-envelope-fill" style="font-size:2.5rem;color:var(--primary);display:block;margin-bottom:.75rem"></i>
                <h3 style="color:var(--primary-dark);margin-bottom:.4rem">{{ __('Correo electrónico') }}</h3>
                <a href="mailto:{{ $email }}" style="color:var(--primary);font-size:1rem;text-decoration:none;word-break:break-all">{{ $email }}</a>
            </div>
            @if($instagram)
            <div style="text-align:center">
                <i class="bi bi-instagram" style="font-size:2.5rem;color:var(--primary);display:block;margin-bottom:.75rem"></i>
                <h3 style="color:var(--primary-dark);margin-bottom:.4rem">Instagram</h3>
                <a href="https://www.instagram.com/{{ ltrim($instagram, '@') }}" target="_blank" rel="noopener" style="color:var(--primary);text-decoration:none">{{ '@' . ltrim($instagram, '@') }}</a>
            </div>
            @endif
        </div>
        <hr style="margin:2rem 0;border-color:var(--selected-bg)">
        <p style="color:#666;text-align:center;line-height:1.7;margin-bottom:1.25rem">
            {{ __('Hacemos las reservas por WhatsApp y por correo electrónico. También puedes reservar directamente desde la web.') }}
        </p>
        <div style="text-align:center">
            <a href="{{ route('servicios') }}" class="btn-salmon"><i class="bi bi-calendar-check"></i> {{ __('Reservar servicio') }}</a>
        </div>
    </div>
</div>
@endsection
