{{--
    Botón «Lo quiero» de un servicio ($servicio). Usa $descuento (calculado una vez en la página).
    - Masajes y yoga con sesión iniciada y cuestionario hecho: abre el calendario (modal).
    - En el resto de casos lleva a la página de reserva (registro, cuestionario o solicitud de curso).
--}}
@php
    $usuario = auth()->user();
    $puedeElegirHora = $usuario && ($usuario->perfilCompleto() || $usuario->is_admin) && $servicio->usaAgenda();
    $precio = \App\Models\Servicio::aplicarDescuento($servicio->t('precio'), $descuento ?? 0);
    if (($descuento ?? 0) > 0) {
        $precio .= ' (−' . $descuento . ' %)';
    }
@endphp
@if($puedeElegirHora)
    <button type="button" class="btn-salmon {{ $clase ?? '' }}" data-reservar data-modo="reservar"
            data-titulo="{{ $servicio->t('titulo') }}"
            data-precio="{{ $servicio->t('duracion') }} · {{ $precio }}"
            data-solo-precio="{{ $precio }}"
            data-disponibilidad="{{ route('reservar.disponibilidad', $servicio) }}"
            data-accion="{{ route('reservar.store', $servicio) }}"
            @if($autoabrir ?? false) data-autoabrir @endif>
        <i class="bi bi-calendar-check"></i> {{ $texto ?? __('Lo quiero') }}
    </button>
@else
    <a href="{{ route('reservar', $servicio) }}" class="btn-salmon {{ $clase ?? '' }}">
        <i class="bi bi-heart"></i> {{ $texto ?? __('Lo quiero') }}
    </a>
@endif
