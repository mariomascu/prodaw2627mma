@extends('layouts.yoguifit')
@section('title', __('Niveles de fidelidad') . ' — YoguiFit')

@push('styles')
<style>
    .niveles { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(240px, 100%), 1fr)); gap: 1.25rem; margin-bottom: 2rem; }
    .nivel-card {
        background: #fff; border-radius: 16px; padding: 1.75rem 1.5rem; text-align: center;
        box-shadow: 0 2px 14px rgba(52,130,137,.1); border-top: 6px solid var(--selected-bg); position: relative;
    }
    .nivel-card.bronze { border-top-color: #c8894a; }
    .nivel-card.silver { border-top-color: #aab4bb; }
    .nivel-card.gold   { border-top-color: #e0b83a; }
    .nivel-card.actual { box-shadow: 0 0 0 3px var(--primary), 0 6px 24px rgba(52,130,137,.2); }
    .nivel-card .tu-nivel {
        position: absolute; top: -.8rem; left: 50%; transform: translateX(-50%);
        background: var(--primary); color: #fff; font-size: .72rem; padding: .2rem .7rem; border-radius: 12px;
        text-transform: uppercase; letter-spacing: .04em; white-space: nowrap;
    }
    .nivel-descuento { font-size: 2.6rem; font-weight: 600; color: var(--primary-dark); margin: 1rem 0 .25rem; }
    .nivel-requisito { color: #666; font-size: .92rem; }
    .como-funciona li { margin-bottom: .4rem; }
</style>
@endpush

@section('content')
<div class="page-banner">
    <h1><i class="bi bi-award"></i> {{ __('Niveles de fidelidad') }}</h1>
    <p>{{ __('Cuantas más sesiones reservas, más ahorras') }}</p>
</div>

<div class="container section" style="max-width:960px">

    @auth
        <div class="card-yf card-pad" style="margin-bottom:2rem;text-align:center">
            <p style="margin-bottom:.5rem">{{ __('Has reservado :numero sesiones.', ['numero' => $sesiones]) }}</p>
            @if($nivel)
                <p>@include('partials.insignia', ['nivel' => $nivel, 'tamano' => 'grande'])</p>
            @endif
            @if($siguiente)
                <div style="margin-top:.75rem">@include('partials.letrero-nivel', ['siguiente' => $siguiente, 'enlazar' => false])</div>
            @endif
        </div>
    @endauth

    <div class="niveles">
        @foreach(array_reverse(\App\Models\User::NIVELES, true) as $clave => [$nombre, $minimo, $descuento])
            <div class="nivel-card {{ $clave }} {{ ($nivel['clave'] ?? null) === $clave ? 'actual' : '' }}">
                @if(($nivel['clave'] ?? null) === $clave)
                    <span class="tu-nivel">{{ __('Tu nivel') }}</span>
                @endif
                @include('partials.insignia', ['nivel' => ['clave' => $clave, 'nombre' => $nombre, 'descuento' => $descuento], 'tamano' => 'grande'])
                <div class="nivel-descuento">−{{ $descuento }} %</div>
                <div class="nivel-requisito">{{ __('Con más de :numero sesiones reservadas', ['numero' => $minimo]) }}</div>
            </div>
        @endforeach
    </div>

    <div class="card-yf card-pad como-funciona">
        <h2>{{ __('¿Cómo funciona?') }}</h2>
        <ul>
            <li>{{ __('Cada sesión que reservas cuenta para tu nivel. Las citas canceladas no cuentan.') }}</li>
            <li>{{ __('Al superar 10, 20 o 30 sesiones subes de nivel automáticamente.') }}</li>
            <li>{{ __('El descuento de tu nivel se aplica solo en tus siguientes reservas y lo verás en el precio antes de confirmar.') }}</li>
            <li>{{ __('Puedes consultar tu nivel en cualquier momento en «Mis citas».') }}</li>
        </ul>
        <div style="margin-top:1.25rem">
            <a href="{{ route('servicios') }}" class="btn-salmon"><i class="bi bi-calendar-check"></i> {{ __('Reservar servicio') }}</a>
        </div>
    </div>
</div>
@endsection
