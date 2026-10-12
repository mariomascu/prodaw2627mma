@extends('layouts.yoguifit')
@section('title', __('Dónde estamos') . ' — YoguiFit')
@section('content')
@php $direccion = \App\Models\Ajuste::valor('centro_direccion'); @endphp
<div class="page-banner">
    <h1><i class="bi bi-geo-alt"></i> {{ __('Dónde estamos') }}</h1>
    <p>{{ __('Masajes en la Axarquía') }}</p>
</div>
<div class="container section" style="max-width:800px">
    <div class="card-yf" style="overflow:hidden">
        <iframe
            src="https://www.google.com/maps?q={{ urlencode('YoguiFit, ' . $direccion) }}&z=16&output=embed&hl={{ substr(app()->getLocale(), 0, 2) }}"
            title="{{ __('Mapa de YoguiFit') }}"
            width="100%" height="380" style="border:0;display:block" allowfullscreen loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
        <div class="card-pad" style="padding-top:1.75rem;padding-bottom:1.75rem">
            <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.5rem">
                <i class="bi bi-pin-map-fill" style="color:var(--salmon);font-size:1.3rem"></i>
                <strong style="color:var(--primary-dark)">{{ __('Dirección') }}</strong>
            </div>
            <p>
                {{ $direccion }}<br>
                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode('YoguiFit, ' . $direccion) }}" target="_blank" rel="noopener" style="color:var(--primary)">
                    <i class="bi bi-box-arrow-up-right"></i> {{ __('Abrir en Google Maps') }}
                </a>
            </p>
            <p style="font-size:.9rem;color:#777">
                <i class="bi bi-info-circle"></i> {{ __('Aconsejamos hacer la reserva con varios días de antelación para poder ofrecer una disponibilidad horaria mayor.') }}
            </p>
        </div>
    </div>
</div>
@endsection
