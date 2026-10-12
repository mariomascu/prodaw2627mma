{{--
    Insignia del nivel de fidelidad. Recibe $nivel = ['clave' => 'gold', 'nombre' => 'Gold', 'descuento' => 20] (o null).
    Si existe public/img/insignias/<clave>.(svg|png|webp) se usa esa imagen; si no, una medalla con el color del nivel.
--}}
@if($nivel)
    @php
        $imagen = collect(['svg', 'png', 'webp'])
            ->map(fn ($ext) => "img/insignias/{$nivel['clave']}.$ext")
            ->first(fn ($ruta) => is_file(public_path($ruta)));
    @endphp
    <span class="insignia insignia-{{ $nivel['clave'] }} {{ $tamano ?? '' }}" title="{{ $nivel['nombre'] }} · −{{ $nivel['descuento'] }} %">
        @if($imagen)
            <img src="{{ asset($imagen) }}" alt="">
        @else
            <i class="bi bi-award-fill"></i>
        @endif
        {{ $nivel['nombre'] }}
    </span>
@endif

@once
@push('styles')
<style>
    .insignia {
        display: inline-flex; align-items: center; gap: .3rem; white-space: nowrap;
        padding: .2rem .65rem .2rem .45rem; border-radius: 20px; font-size: .78rem; font-weight: 600; color: #fff;
    }
    .insignia img { width: 1.2em; height: 1.2em; object-fit: contain; }
    .insignia-bronze { background: linear-gradient(135deg, #b87333, #d9a066); }
    .insignia-silver { background: linear-gradient(135deg, #8e9aa3, #c9d1d6); color: #233; }
    .insignia-gold   { background: linear-gradient(135deg, #c79a1c, #f2d36b); color: #3a2a00; }
    .insignia.grande { font-size: 1rem; padding: .4rem 1rem .4rem .7rem; }
</style>
@endpush
@endonce
