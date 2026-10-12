@extends('layouts.yoguifit')
@section('title', __('Panel') . ' — YoguiFit')

@push('styles')
<style>
    .kpis { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
    .kpi { background: #fff; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 14px rgba(52,130,137,.1); }
    .kpi i { font-size: 1.5rem; color: var(--salmon); }
    .kpi .numero { font-size: 2rem; font-weight: 600; color: var(--primary-dark); line-height: 1.2; margin-top: .3rem; }
    .kpi .texto { font-size: .85rem; color: #777; }
    .kpi .nota { font-size: .75rem; color: #aaa; margin-top: .2rem; }

    .rejilla { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
    .rejilla .caja { margin-bottom: 0; }

    .barra { margin-bottom: .85rem; }
    .barra-texto { display: flex; justify-content: space-between; gap: .75rem; font-size: .86rem; color: #555; margin-bottom: .3rem; }
    .barra-texto strong { color: var(--primary-dark); white-space: nowrap; }
    .barra-texto small { color: #999; font-weight: 400; }
    .barra-fondo { height: 10px; background: var(--selected-bg); border-radius: 5px; overflow: hidden; }
    .barra-relleno { height: 100%; background: linear-gradient(90deg, var(--primary), var(--secondary)); border-radius: 5px; }

    @media (max-width: 900px) {
        .kpis { grid-template-columns: repeat(2, 1fr); }
        .rejilla { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
@include('admin._menu')

<div class="container admin-wrap">

    @if($cambiosPendientes)
        <div class="alert alert-success" style="display:flex;align-items:center;gap:.6rem;flex-wrap:wrap">
            <i class="bi bi-arrow-left-right"></i>
            {{ __('Tienes :numero solicitudes de cambio de cita pendientes.', ['numero' => $cambiosPendientes]) }}
            <a href="{{ route('admin.agenda') }}" style="color:var(--primary);font-weight:600">{{ __('Revisarlas') }}</a>
        </div>
    @endif

    <div class="kpis">
        <div class="kpi">
            <i class="bi bi-people-fill"></i>
            <div class="numero">{{ $totalClientes }}</div>
            <div class="texto">{{ __('Clientes registrados') }}</div>
            @if($clientesSinPerfil)
                <div class="nota">{{ $clientesSinPerfil }} sin cuestionario</div>
            @endif
        </div>
        <div class="kpi">
            <i class="bi bi-hourglass-split"></i>
            <div class="numero">{{ $citasPendientes }}</div>
            <div class="texto">{{ __('Citas pendientes') }}</div>
        </div>
        <div class="kpi">
            <i class="bi bi-calendar-check-fill"></i>
            <div class="numero">{{ $totalCitas }}</div>
            <div class="texto">{{ __('Citas en total') }}</div>
        </div>
        <div class="kpi">
            <i class="bi bi-grid-3x3-gap-fill"></i>
            <div class="numero">{{ $totalServicios }}</div>
            <div class="texto">{{ __('Servicios publicados') }}</div>
        </div>
    </div>

    <div class="rejilla">
        <div class="caja">
            <h3><i class="bi bi-gender-ambiguous"></i> {{ __('Clientes por sexo') }}</h3>
            @include('admin._barras', ['datos' => $porSexo])
        </div>
        <div class="caja">
            <h3><i class="bi bi-bullseye"></i> {{ __('Objetivo principal') }}</h3>
            @include('admin._barras', ['datos' => $porObjetivo])
        </div>
        <div class="caja">
            <h3><i class="bi bi-arrow-repeat"></i> {{ __('Frecuencia deseada') }}</h3>
            @include('admin._barras', ['datos' => $porFrecuencia])
        </div>
        <div class="caja">
            <h3><i class="bi bi-pie-chart"></i> {{ __('Reservas por tipo de servicio') }}</h3>
            @include('admin._barras', ['datos' => $reservasPorTipo])
        </div>
    </div>

    <div class="caja" style="margin-top:1.5rem">
        <h3><i class="bi bi-trophy"></i> {{ __('Servicios más reservados') }}</h3>
        @include('admin._barras', ['datos' => $masReservados->pluck('citas_count', 'titulo')->all()])
    </div>

    <div class="caja">
        <h3><i class="bi bi-clock-history"></i> {{ __('Últimas solicitudes') }}</h3>
        <div class="tabla-scroll">
            <table class="tabla">
                <thead>
                    <tr><th>{{ __('Fecha') }}</th><th>{{ __('Cliente') }}</th><th>{{ __('Servicio') }}</th><th>{{ __('Teléfono') }}</th><th>{{ __('Estado') }}</th></tr>
                </thead>
                <tbody>
                @forelse($ultimasCitas as $cita)
                    <tr>
                        <td>{{ $cita->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($cita->user)
                                <a href="{{ route('admin.clientes.show', $cita->user) }}">{{ $cita->user->name }}</a>
                            @else
                                {{ $cita->nombre }}
                            @endif
                        </td>
                        <td>{{ $cita->servicio->titulo ?? __('Sin servicio') }}</td>
                        <td>{{ $cita->telefono }}</td>
                        <td><span class="badge badge-{{ $cita->estado }}">{{ __(ucfirst($cita->estado)) }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="vacio">{{ __('Todavía no hay solicitudes.') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
