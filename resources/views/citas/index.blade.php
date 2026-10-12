@extends('layouts.yoguifit')
@section('title', __('Mis citas') . ' — YoguiFit')

@push('styles')
<style>
    .citas-wrap { max-width: 860px; margin: 0 auto; padding: 3rem 1.25rem; }

    /* Tarjeta de fidelidad */
    .fidelidad {
        background: #fff; border-radius: 14px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;
        box-shadow: 0 2px 12px rgba(52,130,137,.1); display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;
    }
    .fidelidad-numero { font-size: 2rem; font-weight: 600; color: var(--primary-dark); line-height: 1; }
    .fidelidad-texto { font-size: .85rem; color: #777; }
    .fidelidad-info { flex: 1; min-width: 200px; font-size: .9rem; color: #555; line-height: 1.5; }

    .cita-card {
        background: #fff; border-radius: 14px;
        padding: 1.4rem 1.6rem; margin-bottom: 1.2rem;
        box-shadow: 0 2px 12px rgba(52,130,137,.1);
        border-left: 5px solid var(--primary);
        display: flex; align-items: flex-start; gap: 1.2rem;
        flex-wrap: wrap;
    }
    .cita-card.cita-cancelada { border-left-color: #e3b5b5; opacity: .75; }
    .cita-card.cita-pendiente { border-left-color: #f0c36d; }
    .cita-fecha {
        width: 64px; flex-shrink: 0; text-align: center; border-radius: 10px; overflow: hidden;
        background: var(--selected-bg); color: var(--primary-dark);
    }
    .cita-fecha .mes { background: var(--primary); color: #fff; font-size: .7rem; text-transform: uppercase; padding: .15rem 0; }
    .cita-fecha .dia { font-size: 1.5rem; font-weight: 600; line-height: 1.3; }
    .cita-fecha .hora { font-size: .78rem; padding-bottom: .3rem; }
    .cita-fecha.sin-fecha { display: flex; align-items: center; justify-content: center; height: 64px; }
    .cita-fecha.sin-fecha i { font-size: 1.5rem; color: var(--primary); }
    .cita-info { flex: 1; min-width: 200px; }
    .cita-servicio { font-size: 1rem; font-weight: 600; color: var(--primary-dark); margin-bottom: .3rem; }
    .cita-meta { font-size: .85rem; color: #777; display: flex; gap: .4rem 1rem; flex-wrap: wrap; }
    .cita-meta span { display: flex; align-items: center; gap: .3rem; }
    .cita-aviso { margin-top: .6rem; font-size: .84rem; background: #fff7e6; color: #8a5a00; border-radius: 8px; padding: .45rem .75rem; }
    .cita-acciones { display: flex; gap: .5rem; flex-wrap: wrap; margin-top: .75rem; }
    .cita-acciones .btn-outline-yf, .cita-acciones .btn-cancelar, .cita-acciones .btn-qr { font-size: .82rem; padding: .4rem .95rem; }

    /* Diálogo con el código QR */
    dialog.modal-qr {
        margin: auto; border: none; border-radius: 16px; padding: 1.5rem; width: min(380px, calc(100% - 2rem));
        text-align: center; color: var(--primary-dark); box-shadow: 0 20px 60px rgba(0,0,0,.3);
    }
    dialog.modal-qr::backdrop { background: rgba(10,30,32,.55); }
    .qr-imagen { display: flex; justify-content: center; margin: 1rem 0; }
    .qr-imagen svg, .qr-imagen img { width: 240px; height: 240px; }
    .qr-datos { font-size: .92rem; line-height: 1.6; margin-bottom: .5rem; }
    .qr-ayuda { font-size: .8rem; color: #888; margin-bottom: 1rem; }
    .btn-cancelar {
        display: inline-flex; align-items: center; gap: .3rem; background: #fff; color: #c33;
        border: 2px solid #f3c4c4; border-radius: 25px; cursor: pointer; font-family: 'Geologica', sans-serif;
    }
    .btn-cancelar:hover { background: #fde8e8; }
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
        .fidelidad { padding: 1rem 1.1rem; }
    }
</style>
@endpush

@section('content')

<div class="page-banner">
    <h1><i class="bi bi-clock-history"></i> {{ __('Mis citas') }}</h1>
    <p>{{ __('Tus reservas, cambios y nivel de fidelidad') }}</p>
</div>

<div class="citas-wrap">

    {{-- Nivel de fidelidad --}}
    <div class="fidelidad">
        <div>
            <div class="fidelidad-numero">{{ $sesiones }}</div>
            <div class="fidelidad-texto">{{ __('sesiones reservadas') }}</div>
        </div>
        @if($nivel)
            <a href="{{ route('niveles') }}" title="{{ __('Ver los niveles') }}">@include('partials.insignia', ['nivel' => $nivel, 'tamano' => 'grande'])</a>
        @endif
        <div class="fidelidad-info">
            @if($nivel)
                <p style="margin-bottom:.5rem">{{ __('Tienes un :descuento % de descuento en tus próximas sesiones.', ['descuento' => $nivel['descuento']]) }}</p>
            @endif
            @if($siguiente)
                @include('partials.letrero-nivel', ['siguiente' => $siguiente])
            @else
                <a href="{{ route('niveles') }}" style="color:var(--primary)">{{ __('¡Tienes el nivel más alto!') }}</a>
            @endif
        </div>
    </div>

    @forelse($citas as $cita)
    <div class="cita-card cita-{{ $cita->estado }}">
        @if($cita->inicio)
            <div class="cita-fecha">
                <div class="mes">{{ $cita->inicio->translatedFormat('M') }}</div>
                <div class="dia">{{ $cita->inicio->format('j') }}</div>
                <div class="hora">{{ $cita->inicio->format('H:i') }}</div>
            </div>
        @else
            <div class="cita-fecha sin-fecha"><i class="bi bi-calendar-check"></i></div>
        @endif

        <div class="cita-info">
            <div class="cita-servicio">
                {{ $cita->servicio ? $cita->servicio->t('titulo') : __('Servicio a determinar') }}
            </div>
            <div class="cita-meta">
                @if($cita->inicio)
                    <span><i class="bi bi-calendar3"></i> {{ ucfirst($cita->inicio->translatedFormat('l j \d\e F')) }}, {{ $cita->inicio->format('H:i') }}–{{ $cita->fin?->format('H:i') }}</span>
                @else
                    <span><i class="bi bi-clock"></i> {{ __('Solicitada el :fecha', ['fecha' => $cita->created_at->format('d/m/Y')]) }}</span>
                @endif
                @if($cita->precio_final)
                    <span><i class="bi bi-tag"></i> {{ $cita->precio_final }}@if($cita->descuento) (−{{ $cita->descuento }} %)@endif</span>
                @endif
            </div>

            @if($cita->solicitudPendiente)
                <div class="cita-aviso">
                    <i class="bi bi-hourglass-split"></i>
                    {{ __('Has pedido cambiarla al :fecha a las :hora. Pendiente de aprobación.', [
                        'fecha' => $cita->solicitudPendiente->inicio->translatedFormat('l j \d\e F'),
                        'hora'  => $cita->solicitudPendiente->inicio->format('H:i'),
                    ]) }}
                </div>
            @endif

            @if($cita->validada_at)
                <div class="cita-meta" style="margin-top:.4rem;color:var(--primary)"><span><i class="bi bi-patch-check-fill"></i> {{ __('Sesión realizada el :fecha', ['fecha' => $cita->validada_at->format('d/m/Y H:i')]) }}</span></div>
            @endif

            @if($cita->tieneQr() || $cita->sePuedeCambiar() || $cita->sePuedeCancelar())
            <div class="cita-acciones">
                @if($cita->tieneQr())
                    <button type="button" class="btn-primary-yf btn-qr" data-qr="{{ $cita->urlValidacion() }}"
                            data-qr-titulo="{{ $cita->servicio?->t('titulo') }}"
                            data-qr-fecha="{{ ucfirst($cita->inicio->translatedFormat('l j \d\e F')) }}, {{ $cita->inicio->format('H:i') }}"
                            data-qr-nombre="{{ $cita->nombre }}">
                        <i class="bi bi-qr-code"></i> {{ __('Ver código QR') }}
                    </button>
                @endif
                @if($cita->sePuedeCambiar() && $cita->servicio)
                    <button type="button" class="btn-outline-yf" data-reservar data-modo="cambiar"
                            data-titulo="{{ __('Cambiar cita') }}: {{ $cita->servicio->t('titulo') }}"
                            data-precio="{{ __('Ahora: :fecha a las :hora', ['fecha' => $cita->inicio->translatedFormat('l j \d\e F'), 'hora' => $cita->inicio->format('H:i')]) }}"
                            data-disponibilidad="{{ route('reservar.disponibilidad', $cita->servicio) }}"
                            data-accion="{{ route('citas.cambio', $cita) }}">
                        <i class="bi bi-arrow-left-right"></i> {{ __('Cambiar cita') }}
                    </button>
                @endif
                @if($cita->sePuedeCancelar())
                    <form method="POST" action="{{ route('citas.cancelar', $cita) }}"
                          onsubmit="return confirm(@js(__('¿Seguro que quieres cancelar esta cita?')))">
                        @csrf
                        <button type="submit" class="btn-cancelar"><i class="bi bi-x-circle"></i> {{ __('Cancelar cita') }}</button>
                    </form>
                @endif
            </div>
            @endif
        </div>

        <div>
            <span class="badge badge-{{ $cita->estado }}">{{ __(ucfirst($cita->estado)) }}</span>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <i class="bi bi-calendar-x"></i>
        <h3>{{ __('Todavía no tienes citas') }}</h3>
        <p style="margin-bottom:1.5rem">{{ __('Reserva tu primera sesión y aparecerá aquí.') }}</p>
        <a href="{{ route('servicios') }}" class="btn-primary-yf">
            <i class="bi bi-calendar-plus"></i> {{ __('Reservar servicio') }}
        </a>
    </div>
    @endforelse
</div>

@include('reservar._modal')

{{-- Código QR de la cita: la masajista lo escanea para validar la sesión --}}
<dialog class="modal-qr" id="modalQr">
    <h3 id="qrTitulo"></h3>
    <div class="qr-imagen" id="qrImagen"></div>
    <div class="qr-datos"><strong id="qrNombre"></strong><br><span id="qrFecha"></span></div>
    <p class="qr-ayuda">{{ __('Enseña este código a tu masajista al llegar a la sesión.') }}</p>
    <button type="button" class="btn-outline-yf" onclick="document.getElementById('modalQr').close()">{{ __('Cerrar') }}</button>
</dialog>
@endsection

@push('scripts')
{{-- Generador de códigos QR (https://github.com/kazuhikoarase/qrcode-generator) --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js"></script>
<script>
document.querySelectorAll('[data-qr]').forEach(boton => {
    boton.addEventListener('click', () => {
        const qr = qrcode(0, 'M');      // tamaño automático, corrección de errores media
        qr.addData(boton.dataset.qr);   // el QR contiene el enlace de validación de la cita
        qr.make();

        document.getElementById('qrImagen').innerHTML = qr.createSvgTag({ cellSize: 6, margin: 2, scalable: true });
        document.getElementById('qrTitulo').textContent = boton.dataset.qrTitulo;
        document.getElementById('qrNombre').textContent = boton.dataset.qrNombre;
        document.getElementById('qrFecha').textContent = boton.dataset.qrFecha;
        document.getElementById('modalQr').showModal();
    });
});
</script>
@endpush
