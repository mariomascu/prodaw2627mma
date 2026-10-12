{{-- Resumen del servicio elegido (se usa en las dos páginas de la reserva) --}}
<div class="resumen-servicio">
    @if($servicio->imagen_url)
        <img src="{{ $servicio->imagen_url }}" alt="{{ $servicio->t('titulo') }}">
    @endif
    <div class="resumen-texto">
        <span class="resumen-tipo">{{ __(\App\Models\Servicio::TIPOS[$servicio->tipo] ?? $servicio->tipo) }}</span>
        <h2>{{ $servicio->t('titulo') }}</h2>
        <p>{{ $servicio->t('descripcion') }}</p>
        <div class="resumen-chips">
            <span class="chip-yf"><i class="bi bi-clock"></i> {{ $servicio->t('duracion') }}</span>
            <span class="chip-yf chip-precio"><i class="bi bi-tag"></i> {{ $servicio->t('precio') }}</span>
        </div>
    </div>
</div>
