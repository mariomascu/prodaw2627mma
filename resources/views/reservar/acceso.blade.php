@extends('layouts.yoguifit')
@section('title', __('Reservar') . ' — YoguiFit')
@include('reservar._estilos')

@section('content')
<div class="page-banner">
    <h1><i class="bi bi-calendar-check"></i> {{ __('Reservar') }}</h1>
    <p>{{ __('Para reservar necesitas una cuenta de cliente') }}</p>
</div>

<div class="reserva-wrap">
    <a href="{{ route('servicios', ['tipo' => $servicio->tipo]) }}" class="volver"><i class="bi bi-arrow-left"></i> {{ __('Volver a los servicios') }}</a>

    <div class="reserva-card">
        @include('reservar._servicio')

        <div class="reserva-accion">
            <h3><i class="bi bi-person-plus" style="color:var(--salmon)"></i> {{ __('Crea tu cuenta para reservar') }}</h3>
            <p>
                {{ __('Regístrate en un momento y responde a 4 preguntas rápidas (solo la primera vez). Después volverás aquí para confirmar tu reserva.') }}
            </p>
            <div class="reserva-botones">
                <a href="{{ route('register') }}" class="btn-salmon"><i class="bi bi-person-plus"></i> {{ __('Registrarme') }}</a>
                <a href="{{ route('login') }}" class="btn-outline-yf"><i class="bi bi-box-arrow-in-right"></i> {{ __('Ya tengo cuenta') }}</a>
            </div>
            @if(\App\Http\Controllers\Auth\GoogleController::configurado())
                <div style="margin-top:1rem;max-width:340px">
                    @include('auth._boton-google', ['separador' => false])
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
