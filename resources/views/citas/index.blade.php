@extends('layouts.yoguifit')
@section('title', 'Mis citas — YoguiFit')

@push('styles')
<style>
    .citas-wrap { max-width: 860px; margin: 0 auto; padding: 3rem 1.25rem; }
    .cita-card {
        background: #fff; border-radius: 14px;
        padding: 1.4rem 1.6rem; margin-bottom: 1.2rem;
        box-shadow: 0 2px 12px rgba(52,130,137,.1);
        border-left: 5px solid var(--primary);
        display: flex; align-items: flex-start; gap: 1.2rem;
        flex-wrap: wrap;
    }
    .cita-icon {
        width: 48px; height: 48px; border-radius: 50%;
        background: var(--selected-bg); display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .cita-icon i { font-size: 1.4rem; color: var(--primary); }
    .cita-info { flex: 1; min-width: 200px; }
    .cita-servicio { font-size: 1rem; font-weight: 600; color: var(--primary-dark); margin-bottom: .3rem; }
    .cita-meta { font-size: .85rem; color: #777; display: flex; gap: 1rem; flex-wrap: wrap; }
    .cita-meta span { display: flex; align-items: center; gap: .3rem; }
    .badge {
        padding: .3rem .85rem; border-radius: 20px; font-size: .78rem; font-weight: 600;
        text-transform: uppercase; letter-spacing: .04em;
    }
    .badge-pendiente { background: #fff3cd; color: #856404; }
    .badge-confirmada { background: var(--selected-bg); color: var(--primary-dark); }
    .badge-cancelada { background: #fde8e8; color: #700; }

    .empty-state { text-align: center; padding: 4rem 1rem; color: #999; }
    .empty-state i { font-size: 4rem; display: block; margin-bottom: 1rem; color: var(--terciario); }
    .empty-state h3 { font-size: 1.2rem; margin-bottom: .5rem; color: #bbb; }

    @media (max-width: 768px) {
        .citas-wrap { padding: 2rem 1rem; }
        .cita-card { padding: 1.1rem 1.1rem; gap: .9rem; }
        .cita-info { min-width: 0; flex-basis: calc(100% - 60px); }
    }
</style>
@endpush

@section('content')

<div class="page-banner">
    <h1><i class="bi bi-clock-history"></i> Mis citas</h1>
    <p>Historial de tus solicitudes de reserva</p>
</div>

<div class="citas-wrap">
    @forelse($citas as $cita)
    <div class="cita-card">
        <div class="cita-icon">
            <i class="bi bi-calendar-check"></i>
        </div>
        <div class="cita-info">
            <div class="cita-servicio">
                {{ $cita->servicio ? $cita->servicio->titulo : 'Servicio a determinar' }}
            </div>
            <div class="cita-meta">
                <span><i class="bi bi-person"></i> {{ $cita->nombre }}</span>
                <span><i class="bi bi-telephone"></i> {{ $cita->telefono }}</span>
                @if($cita->sexo)
                <span><i class="bi bi-people"></i> {{ ucfirst($cita->sexo) }}</span>
                @endif
                <span><i class="bi bi-clock"></i> {{ $cita->created_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>
        <div>
            <span class="badge badge-{{ $cita->estado }}">{{ ucfirst($cita->estado) }}</span>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <i class="bi bi-calendar-x"></i>
        <h3>Todavía no tienes citas</h3>
        <p style="margin-bottom:1.5rem">Reserva tu primera sesión y aparecerá aquí.</p>
        <a href="{{ route('cuestionario') }}" class="btn-primary-yf">
            <i class="bi bi-calendar-plus"></i> Reservar sesión
        </a>
    </div>
    @endforelse
</div>

@endsection
