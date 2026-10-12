@extends('layouts.yoguifit')
@section('title', __('Agenda') . ' — YoguiFit')

@push('styles')
<style>
    .agenda-cabecera { display: flex; align-items: center; gap: .75rem; flex-wrap: wrap; margin-bottom: 1.25rem; }
    .agenda-mes { font-size: 1.4rem; color: var(--primary-dark); text-transform: capitalize; min-width: 180px; text-align: center; }
    .agenda-cabecera .acciones { margin-left: auto; display: flex; gap: .5rem; flex-wrap: wrap; }

    .leyenda { display: flex; gap: .4rem 1.2rem; flex-wrap: wrap; font-size: .82rem; color: #666; margin-bottom: 1rem; }
    .leyenda span { display: inline-flex; align-items: center; gap: .35rem; }
    .leyenda i { width: 14px; height: 14px; border-radius: 4px; display: inline-block; }

    .dias { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(280px, 100%), 1fr)); gap: 1rem; }
    .dia { background: #fff; border-radius: 12px; box-shadow: 0 2px 10px rgba(52,130,137,.08); padding: .9rem 1rem; }
    .dia h4 { font-size: .92rem; color: var(--primary-dark); margin-bottom: .6rem; text-transform: capitalize; }
    .horas { display: flex; flex-direction: column; gap: .3rem; }
    .hora-fila { display: flex; align-items: center; gap: .5rem; }
    .hora-fila form { margin: 0; }
    .hora {
        width: 58px; flex-shrink: 0; padding: .3rem 0; border-radius: 6px; border: none; font-family: 'Geologica', sans-serif;
        font-size: .82rem; font-weight: 500; text-align: center; cursor: pointer;
    }
    .hora-libre, .leyenda .hora-libre { background: #e3f4ef; color: #1f6f5c; }
    .hora-bloqueada, .leyenda .hora-bloqueada { background: #e6e6e6; color: #888; text-decoration: line-through; }
    .hora-reservada, .leyenda .hora-reservada { background: var(--primary); color: #fff; cursor: default; }
    .hora-apartada, .leyenda .hora-apartada { background: #f7d58b; color: #6b4a00; cursor: default; }
    .hora-detalle { font-size: .8rem; color: #555; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .hora-detalle a { color: var(--primary); }

    .solicitud { display: flex; align-items: center; gap: .75rem; flex-wrap: wrap; padding: .7rem 0; border-bottom: 1px solid var(--selected-bg); font-size: .9rem; }
    .solicitud:last-child { border-bottom: none; }
    .solicitud .texto { flex: 1; min-width: 240px; color: #444; line-height: 1.5; }
    .solicitud form { margin: 0; }
</style>
@endpush

@section('content')
@include('admin._menu')

<div class="container admin-wrap">

    {{-- Solicitudes de cambio pendientes --}}
    @if($solicitudes->isNotEmpty())
        <div class="caja">
            <h3><i class="bi bi-arrow-left-right"></i> {{ __('Solicitudes de cambio') }} ({{ $solicitudes->count() }})</h3>
            @foreach($solicitudes as $solicitud)
                <div class="solicitud">
                    <div class="texto">
                        <strong><a href="{{ route('admin.clientes.show', $solicitud->cita->user) }}" style="color:var(--primary)">{{ $solicitud->cita->user->name }}</a></strong>
                        · {{ $solicitud->cita->servicio->titulo ?? __('Sin servicio') }}<br>
                        {{ $solicitud->cita->inicio?->translatedFormat('D j M, H:i') }}
                        <i class="bi bi-arrow-right"></i>
                        <strong>{{ $solicitud->inicio->translatedFormat('D j M, H:i') }}</strong>
                    </div>
                    <form method="POST" action="{{ route('admin.solicitudes.aprobar', $solicitud) }}">
                        @csrf
                        <button type="submit" class="btn-primary-yf btn-peq"><i class="bi bi-check-lg"></i> {{ __('Aprobar') }}</button>
                    </form>
                    <form method="POST" action="{{ route('admin.solicitudes.rechazar', $solicitud) }}">
                        @csrf
                        <button type="submit" class="btn-peligro"><i class="bi bi-x-lg"></i> {{ __('Rechazar') }}</button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Mes y acciones --}}
    <div class="agenda-cabecera">
        <a href="{{ route('admin.agenda', ['mes' => $mes->copy()->subMonth()->format('Y-m')]) }}" class="btn-outline-yf btn-peq" aria-label="{{ __('Mes anterior') }}"><i class="bi bi-chevron-left"></i></a>
        <span class="agenda-mes">{{ $mes->translatedFormat('F Y') }}</span>
        <a href="{{ route('admin.agenda', ['mes' => $mes->copy()->addMonth()->format('Y-m')]) }}" class="btn-outline-yf btn-peq" aria-label="{{ __('Mes siguiente') }}"><i class="bi bi-chevron-right"></i></a>

        <div class="acciones">
            <form method="POST" action="{{ route('admin.agenda.abrir') }}">
                @csrf
                <input type="hidden" name="mes" value="{{ $mes->format('Y-m') }}">
                <button type="submit" class="btn-salmon btn-peq"><i class="bi bi-calendar-plus"></i> {{ $franjas->isEmpty() ? __('Abrir mes') : __('Completar mes') }}</button>
            </form>
            @if($franjas->isNotEmpty())
                <form method="POST" action="{{ route('admin.agenda.cerrar') }}" onsubmit="return confirm(@js(__('¿Quitar todas las horas libres de este mes? Las reservas se mantienen.')))">
                    @csrf
                    <input type="hidden" name="mes" value="{{ $mes->format('Y-m') }}">
                    <button type="submit" class="btn-peligro"><i class="bi bi-calendar-x"></i> {{ __('Quitar horas libres') }}</button>
                </form>
            @endif
        </div>
    </div>

    @if($franjas->isEmpty())
        <div class="caja vacio">
            <i class="bi bi-calendar2-week" style="font-size:2.5rem;display:block;margin-bottom:.5rem;color:var(--terciario)"></i>
            {{ __('Este mes todavía no está abierto. Pulsa «Abrir mes» para crear las horas según el horario del centro.') }}
            <div style="margin-top:.75rem"><a href="{{ route('admin.configuracion', ['pestana' => 'horario']) }}" style="color:var(--primary)">{{ __('Ver el horario') }}</a></div>
        </div>
    @else
        <div class="leyenda">
            <span><i class="hora-libre"></i> {{ __('Libre (pulsa para bloquear)') }}</span>
            <span><i class="hora-bloqueada"></i> {{ __('Bloqueada') }}</span>
            <span><i class="hora-reservada"></i> {{ __('Reservada') }}</span>
            <span><i class="hora-apartada"></i> {{ __('Apartada para un cambio') }}</span>
        </div>

        <div class="dias">
            @foreach($franjas as $fecha => $delDia)
                <div class="dia">
                    <h4>{{ \Carbon\Carbon::parse($fecha)->translatedFormat('l j') }}</h4>
                    <div class="horas">
                        @foreach($delDia as $franja)
                            @php $cita = $franja->ocupacion?->cita; @endphp
                            <div class="hora-fila">
                                @if($cita)
                                    <span class="hora {{ $franja->ocupacion->solicitud_cambio_id ? 'hora-apartada' : 'hora-reservada' }}">{{ $franja->inicio->format('H:i') }}</span>
                                    <span class="hora-detalle">
                                        @if($cita->user)
                                            <a href="{{ route('admin.clientes.show', $cita->user) }}">{{ $cita->user->name }}</a>
                                        @else
                                            {{ $cita->nombre }}
                                        @endif
                                        · {{ $cita->servicio->titulo ?? '' }}
                                    </span>
                                @else
                                    <form method="POST" action="{{ route('admin.agenda.bloquear', $franja) }}">
                                        @csrf
                                        <button type="submit" class="hora {{ $franja->bloqueada ? 'hora-bloqueada' : 'hora-libre' }}"
                                                title="{{ $franja->bloqueada ? __('Desbloquear') : __('Bloquear') }}">{{ $franja->inicio->format('H:i') }}</button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
