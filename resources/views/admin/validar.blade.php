@extends('layouts.yoguifit')
@section('title', __('Validar sesión') . ' — YoguiFit')

@push('styles')
<style>
    .validar-wrap { max-width: 560px; margin: 0 auto; padding: 2rem 1rem 3rem; }
    .validar-card { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(52,130,137,.12); overflow: hidden; }
    .validar-estado { padding: 1rem 1.25rem; font-weight: 500; display: flex; align-items: center; gap: .6rem; }
    .validar-estado.pendiente { background: var(--selected-bg); color: var(--primary-dark); }
    .validar-estado.realizada { background: #e3f4ef; color: #1f6f5c; }
    .validar-estado.cancelada { background: #fde8e8; color: #700; }
    .validar-estado i { font-size: 1.4rem; }
    .validar-datos { padding: 1.25rem; }
    .validar-datos dl { display: grid; grid-template-columns: auto 1fr; gap: .55rem 1rem; font-size: .92rem; }
    .validar-datos dt { color: #888; }
    .validar-datos dd { color: var(--primary-dark); font-weight: 500; }
    .validar-aviso { margin: 0 1.25rem 1rem; background: #fff7e6; color: #8a5a00; border-radius: 8px; padding: .6rem .85rem; font-size: .88rem; }
    .validar-acciones { padding: 0 1.25rem 1.25rem; display: flex; gap: .75rem; flex-wrap: wrap; }
    .validar-acciones .btn-salmon { font-size: 1rem; padding: .8rem 1.6rem; }
</style>
@endpush

@section('content')
<div class="validar-wrap">
    <div class="validar-card">
        @if($cita->estado === 'cancelada')
            <div class="validar-estado cancelada"><i class="bi bi-x-circle-fill"></i> {{ __('Cita cancelada') }}</div>
        @elseif($cita->validada_at)
            <div class="validar-estado realizada"><i class="bi bi-patch-check-fill"></i> {{ __('Sesión realizada el :fecha', ['fecha' => $cita->validada_at->format('d/m/Y H:i')]) }}</div>
        @else
            <div class="validar-estado pendiente"><i class="bi bi-qr-code"></i> {{ __('Cita pendiente de validar') }}</div>
        @endif

        <div class="validar-datos">
            <dl>
                <dt>{{ __('Cliente') }}</dt>
                <dd>
                    @if($cita->user)
                        <a href="{{ route('admin.clientes.show', $cita->user) }}" style="color:var(--primary)">{{ $cita->user->name }}</a>
                        @include('partials.insignia', ['nivel' => $nivel])
                    @else
                        {{ $cita->nombre }}
                    @endif
                </dd>
                <dt>{{ __('Teléfono') }}</dt>
                <dd><a href="tel:{{ preg_replace('/[^0-9+]/', '', $cita->telefono) }}" style="color:var(--primary)">{{ $cita->telefono }}</a></dd>
                <dt>{{ __('Servicio') }}</dt>
                <dd>{{ $cita->servicio->titulo ?? __('Sin servicio') }}</dd>
                <dt>{{ __('Día y hora') }}</dt>
                <dd>{{ $cita->inicio ? ucfirst($cita->inicio->translatedFormat('l j \d\e F')) . ', ' . $cita->inicio->format('H:i') . '–' . $cita->fin?->format('H:i') : '—' }}</dd>
                <dt>{{ __('Precio') }}</dt>
                <dd>{{ $cita->precio_final ?? $cita->servicio?->precio ?? '—' }}@if($cita->descuento) (−{{ $cita->descuento }} %)@endif</dd>
                <dt>{{ __('Sesiones') }}</dt>
                <dd>{{ $sesiones }}</dd>
                @if($cita->notas)
                    <dt>{{ __('Comentarios') }}</dt>
                    <dd style="font-weight:400">{{ $cita->notas }}</dd>
                @endif
            </dl>
        </div>

        @if($cita->inicio && ! $cita->inicio->isToday() && ! $cita->validada_at && $cita->estado !== 'cancelada')
            <div class="validar-aviso"><i class="bi bi-exclamation-triangle"></i> {{ __('Atención: esta cita no es para hoy.') }}</div>
        @endif

        <div class="validar-acciones">
            @if(! $cita->validada_at && $cita->estado !== 'cancelada')
                <form method="POST" action="{{ route('admin.citas.validar.store', ['cita' => $cita, 'codigo' => $codigo]) }}">
                    @csrf
                    <button type="submit" class="btn-salmon"><i class="bi bi-check2-circle"></i> {{ __('Validar sesión') }}</button>
                </form>
            @endif
            <a href="{{ route('admin.escaner') }}" class="btn-outline-yf"><i class="bi bi-qr-code-scan"></i> {{ __('Escanear otro código') }}</a>
        </div>
    </div>
</div>
@endsection
