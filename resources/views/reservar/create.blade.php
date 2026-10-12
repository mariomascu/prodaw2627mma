@extends('layouts.yoguifit')
@section('title', __('Confirmar reserva') . ' — YoguiFit')
@include('reservar._estilos')

@section('content')
<div class="page-banner">
    <h1><i class="bi bi-calendar-check"></i> {{ __('Confirmar reserva') }}</h1>
    <p>{{ __('Revisa el servicio y envíanos tu solicitud') }}</p>
</div>

<div class="reserva-wrap">
    <a href="{{ route('servicios', ['tipo' => $servicio->tipo]) }}" class="volver"><i class="bi bi-arrow-left"></i> {{ __('Volver a los servicios') }}</a>

    <div class="reserva-card">
        @include('reservar._servicio')

        @if($servicio->usaAgenda())
            {{-- Masajes y yoga: se elige día y hora en el calendario --}}
            @php $descuento = $user->nivel()['descuento'] ?? 0; @endphp
            <div class="reserva-accion">
                <h3>{{ __('Elige día y hora') }}</h3>
                <p>{{ __('Solo aparecen las horas libres. La reserva queda confirmada al momento.') }}</p>
                <div class="reserva-botones">
                    @include('reservar._boton', ['texto' => __('Elegir día y hora'), 'autoabrir' => ! $errors->any()])
                </div>
            </div>
        @else
        <form method="POST" action="{{ route('reservar.store', $servicio) }}" class="reserva-accion">
            @csrf
            <h3>{{ __('Tus datos') }}</h3>
            <p>
                {{ __('Reservas como') }} <strong>{{ $user->name }}</strong> ({{ $user->email }}).
                {{ __('Te llamaremos o escribiremos por WhatsApp para confirmar el día y la hora.') }}
            </p>

            <div class="form-group">
                <label for="telefono"><i class="bi bi-telephone"></i> {{ __('Teléfono de contacto') }}</label>
                <input type="tel" id="telefono" name="telefono" value="{{ old('telefono', $user->telefono) }}" required>
                @error('telefono') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="notas"><i class="bi bi-chat-left-text"></i> {{ __('Comentarios (opcional)') }}</label>
                <textarea id="notas" name="notas" rows="3" placeholder="{{ __('Días u horas que te vienen mejor, lesiones a tener en cuenta…') }}">{{ old('notas') }}</textarea>
                @error('notas') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="reserva-botones">
                <button type="submit" class="btn-salmon"><i class="bi bi-send"></i> {{ __('Enviar solicitud') }}</button>
            </div>
        </form>
        @endif
    </div>
</div>

@include('reservar._modal')
@endsection
