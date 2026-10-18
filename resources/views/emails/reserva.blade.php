@extends('emails.layout')

@php
    $locale   = app()->getLocale();
    $servicio = $cita->servicio;
    $fila     = 'padding:7px 0;border-bottom:1px solid #e3ecea;vertical-align:top';
    $etiqueta = $fila . ';color:#6b8f92;width:38%';
@endphp

@section('contenido')
    <h1 style="font-size:22px;color:#123437;margin:0 0 12px">
        {{ $cambio ? __('Tu cita ha cambiado') : __('¡Tu cita está confirmada!') }}
    </h1>
    <p style="margin:0 0 18px">
        {{ __('Hola, :nombre.', ['nombre' => $cita->nombre]) }}
        {{ $cambio ? __('Hemos aprobado el cambio de tu cita. Estos son los nuevos datos:') : __('Estos son los datos de tu reserva:') }}
    </p>

    {{-- Datos de la cita --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:15px">
        <tr><td style="{{ $etiqueta }}">{{ __('Servicio') }}</td><td style="{{ $fila }};font-weight:bold">{{ $servicio?->t('titulo') }}</td></tr>
        <tr><td style="{{ $etiqueta }}">{{ __('Día') }}</td><td style="{{ $fila }};font-weight:bold">{{ ucfirst($cita->inicio->locale($locale)->isoFormat('dddd, LL')) }}</td></tr>
        <tr><td style="{{ $etiqueta }}">{{ __('Hora') }}</td><td style="{{ $fila }};font-weight:bold">{{ $cita->inicio->format('H:i') }} – {{ $cita->fin?->format('H:i') }}</td></tr>
        @if($servicio?->tipo === 'masajes')
            <tr><td style="{{ $etiqueta }}">{{ __('Duración') }}</td><td style="{{ $fila }}">{{ $servicio->t('duracion') }}</td></tr>
        @endif
        <tr>
            <td style="{{ $etiqueta }}">{{ __('Precio') }}</td>
            <td style="{{ $fila }}">
                @if($cita->precio_final)
                    {{ $cita->precio_final }}@if($cita->descuento) (−{{ $cita->descuento }} %)@endif
                @elseif($cita->bono_id)
                    {{ __('Sesión de tu bono') }}
                @else
                    {{ $servicio?->t('precio') }}
                @endif
                @if($cita->pago?->estado === 'pagado')
                    <span style="color:#348289;font-weight:bold"> · {{ __('Pagado') }}</span>
                @endif
            </td>
        </tr>
        @if($cita->bono)
            <tr><td style="{{ $etiqueta }}">{{ __('Bono') }}</td><td style="{{ $fila }}">{{ __('Te quedan :restantes de :total sesiones', ['restantes' => $cita->bono->sesionesRestantes(), 'total' => $cita->bono->sesiones_totales]) }}</td></tr>
        @endif
        @if($cita->notas)
            <tr><td style="{{ $etiqueta }}">{{ __('Comentarios') }}</td><td style="{{ $fila }}">{{ $cita->notas }}</td></tr>
        @endif
        <tr><td style="{{ $etiqueta }}">{{ __('Dirección') }}</td><td style="{{ $fila }}">
            {{ \App\Models\Ajuste::valor('centro_direccion') }}<br>
            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode('YoguiFit, ' . \App\Models\Ajuste::valor('centro_direccion')) }}" style="color:#348289">{{ __('Abrir en Google Maps') }}</a>
        </td></tr>
    </table>

    {{-- Código QR (imagen incrustada en el correo; también va adjunto) --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:24px 0 6px;background:#eef5f4;border-radius:12px">
        <tr><td align="center" style="padding:20px">
            <div style="font-weight:bold;color:#123437;margin-bottom:10px">{{ __('Tu código QR') }}</div>
            <img src="{{ $message->embedData($qrPng, 'codigo-qr.png', 'image/png') }}" width="220" height="220" alt="{{ __('Código QR de tu cita') }}" style="display:block;border:0;background:#ffffff">
            <div style="font-size:13px;color:#555555;margin-top:10px">{{ __('Enseña este código a tu masajista al llegar a la sesión.') }}</div>
        </td></tr>
    </table>

    @include('emails._boton', ['url' => route('citas'), 'texto' => __('Ver mis citas')])

    <p style="font-size:13px;color:#7a8a8a;margin:0 0 6px">
        {{ __('Puedes pedir un cambio de cita desde «Mis citas». Si necesitas cancelarla, hazlo con al menos 24 horas de antelación; si estaba pagada, te devolveremos el importe.') }}
    </p>
@endsection
