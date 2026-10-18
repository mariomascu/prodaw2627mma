{{--
    Botón «Lo quiero» de un servicio ($servicio). Usa $descuento (calculado una vez en la página).
    - Masajes y yoga con sesión iniciada y cuestionario hecho: abre el calendario (modal).
    - En el resto de casos lleva a la página de reserva (registro, cuestionario o solicitud de curso).
    data-pago="1" avisa al modal de que al confirmar se irá a pagar con Stripe.
--}}
@php
    $usuario = auth()->user();
    $puedeElegirHora = $usuario && ($usuario->perfilCompleto() || $usuario->is_admin) && $servicio->usaAgenda();
    $precio = \App\Models\Servicio::aplicarDescuento($servicio->t('precio'), $descuento ?? 0);
    if (($descuento ?? 0) > 0) {
        $precio .= ' (−' . $descuento . ' %)';
    }

    // Yoga: si el cliente tiene un bono con sesiones libres, la sesión no se cobra
    $bono = ($puedeElegirHora && $servicio->tipo === 'yoga') ? $usuario->bonoDisponible($servicio) : null;
    if ($bono) {
        $precio = __('Incluida en tu bono (te quedan :numero)', ['numero' => $bono->sesionesLibres()]);
    } elseif ($servicio->tipo === 'yoga') {
        $precio .= ' · ' . __('pack de :numero sesiones', ['numero' => $servicio->sesionesPack()]);
    }
    $pagara = $puedeElegirHora && ! $bono && \App\Services\Pagos::configurado();
@endphp
@if($puedeElegirHora)
    <button type="button" class="btn-salmon {{ $clase ?? '' }}" data-reservar data-modo="reservar"
            data-titulo="{{ $servicio->t('titulo') }}"
            data-precio="{{ $servicio->t('duracion') }} · {{ $precio }}"
            data-solo-precio="{{ $precio }}"
            data-pago="{{ $pagara ? 1 : 0 }}"
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
