@extends('layouts.yoguifit')
@section('title', 'Servicios — YoguiFit')

@push('styles')
<style>
    .tabs-nav {
        display: flex;
        justify-content: center;
        gap: .5rem;
        padding: 1.5rem 1rem;
        background: #fff;
        border-bottom: 2px solid var(--selected-bg);
        flex-wrap: wrap;
    }
    .tab-btn {
        padding: .6rem 1.8rem;
        border-radius: 25px;
        border: 2px solid var(--primary);
        background: transparent;
        color: var(--primary);
        font-family: 'Geologica', sans-serif;
        font-size: .9rem;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        transition: all .2s;
        display: flex; align-items: center; gap: .4rem;
        text-transform: uppercase; letter-spacing: .05em;
    }
    .tab-btn:hover, .tab-btn.active {
        background: var(--primary);
        color: #fff;
    }

    .servicios-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(300px, 100%), 1fr));
        gap: 1.5rem;
        padding-top: 2.5rem; padding-bottom: 2.5rem;
    }

    .servicio-card {
        background: #fff;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 2px 14px rgba(52,130,137,.1);
        transition: transform .2s, box-shadow .2s;
        display: flex; flex-direction: column;
    }
    .servicio-card:hover { transform: translateY(-4px); box-shadow: 0 8px 28px rgba(52,130,137,.2); }

    /* Entrada escalonada de las tarjetas */
    .servicio-card {
        opacity: 0;
        animation: cardIn .55s cubic-bezier(.2,.7,.3,1) forwards;
        animation-delay: calc(var(--i, 0) * 90ms);
    }
    @keyframes cardIn {
        from { opacity: 0; transform: translateY(18px); }
        to   { opacity: 1; transform: none; }
    }

    .servicio-img {
        height: 200px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        display: flex; align-items: center; justify-content: center;
        position: relative; overflow: hidden;
    }
    .servicio-img > i { font-size: 5rem; color: rgba(255,255,255,.35); }

    /* Brillo mientras la imagen carga */
    .servicio-img::before {
        content: '';
        position: absolute; inset: 0;
        background: linear-gradient(100deg, transparent 20%, rgba(255,255,255,.22) 50%, transparent 80%);
        transform: translateX(-100%);
        animation: shimmer 1.4s ease-in-out infinite;
        z-index: 1;
    }
    .servicio-img.is-loaded::before { display: none; }
    @keyframes shimmer { to { transform: translateX(100%); } }

    .servicio-img img {
        position: absolute; inset: 0;
        width: 100%; height: 100%;
        object-fit: cover;
        opacity: 0;
        transform: scale(1.08);
        filter: blur(6px);
        transition: opacity .6s ease, transform .9s ease, filter .6s ease;
        z-index: 2;
    }
    .servicio-img.is-loaded img { opacity: 1; transform: scale(1); filter: none; }
    .servicio-card:hover .servicio-img.is-loaded img { transform: scale(1.06); }

    /* Velo inferior para que la imagen se funda con la tarjeta */
    .servicio-img::after {
        content: '';
        position: absolute; inset: 0;
        background: linear-gradient(180deg, rgba(18,52,55,.05) 55%, rgba(18,52,55,.35) 100%);
        z-index: 3;
        pointer-events: none;
    }

    .servicio-img .tipo-badge {
        z-index: 4;
        box-shadow: 0 2px 8px rgba(0,0,0,.18);
        position: absolute; top: .75rem; right: .75rem;
        background: var(--salmon); color: #fff;
        padding: .2rem .7rem; border-radius: 12px; font-size: .75rem; font-weight: 600;
        text-transform: uppercase; letter-spacing: .04em;
    }

    .servicio-body { padding: 1.25rem; flex: 1; display: flex; flex-direction: column; }
    .servicio-titulo {
        font-size: 1.1rem; font-weight: 600;
        color: var(--primary-dark); margin-bottom: .5rem;
    }
    .servicio-desc {
        font-size: .88rem; color: #666; line-height: 1.6;
        flex: 1; margin-bottom: 1rem;
    }
    .servicio-meta {
        display: flex; gap: .75rem; margin-bottom: 1rem;
        flex-wrap: wrap;
    }
    .meta-chip {
        display: flex; align-items: center; gap: .3rem;
        background: var(--selected-bg); color: var(--primary-dark);
        padding: .3rem .75rem; border-radius: 20px; font-size: .8rem; font-weight: 500;
    }
    .meta-chip.precio { background: var(--salmon); color: #fff; }

    .servicio-footer { border-top: 1px solid var(--selected-bg); padding-top: .75rem; }

    @media (max-width: 768px) {
        .tabs-nav { padding: 1rem; }
        .tab-btn { padding: .5rem 1.1rem; font-size: .8rem; }
        .servicios-grid { padding-top: 1.5rem; padding-bottom: 2rem; gap: 1rem; }
        .servicio-img { height: 180px; }
    }

    @media (prefers-reduced-motion: reduce) {
        .servicio-card { animation: none; opacity: 1; }
        .servicio-img::before { display: none; }
        .servicio-img img { transition: opacity .3s; transform: none; filter: none; }
    }
</style>
@endpush

@section('content')

<div class="page-banner">
    <h1><i class="bi bi-grid-3x3-gap"></i> Servicios</h1>
    <p>Descubre toda nuestra oferta de masajes, yoga y formación</p>
</div>

<div class="tabs-nav">
    <a href="{{ route('servicios', ['tipo' => 'cursos']) }}"
       class="tab-btn {{ $tipo === 'cursos' ? 'active' : '' }}">
        <i class="bi bi-mortarboard"></i> Cursos
    </a>
    <a href="{{ route('servicios', ['tipo' => 'masajes']) }}"
       class="tab-btn {{ $tipo === 'masajes' ? 'active' : '' }}">
        <i class="bi bi-hand-index-thumb"></i> Masajes
    </a>
    <a href="{{ route('servicios', ['tipo' => 'yoga']) }}"
       class="tab-btn {{ $tipo === 'yoga' ? 'active' : '' }}">
        <i class="bi bi-flower1"></i> Yoga
    </a>
</div>

<div class="container">
    <div class="servicios-grid">
        @forelse($servicios as $servicio)
        <article class="servicio-card" style="--i: {{ $loop->index }}">
            <div class="servicio-img {{ $servicio->imagen_url ? '' : 'is-loaded' }}">
                @if($servicio->imagen_url)
                    <img src="{{ $servicio->imagen_url }}" alt="{{ $servicio->titulo }}"
                         loading="{{ $loop->index < 3 ? 'eager' : 'lazy' }}" decoding="async"
                         onload="this.parentElement.classList.add('is-loaded')"
                         onerror="this.parentElement.classList.add('is-loaded'); this.remove()">
                @endif
                @if($servicio->tipo === 'cursos')
                    <i class="bi bi-mortarboard-fill"></i>
                @elseif($servicio->tipo === 'masajes')
                    <i class="bi bi-hand-index-thumb-fill"></i>
                @else
                    <i class="bi bi-flower1"></i>
                @endif
                <span class="tipo-badge">{{ ucfirst($servicio->tipo) }}</span>
            </div>
            <div class="servicio-body">
                <h2 class="servicio-titulo">{{ $servicio->titulo }}</h2>
                <p class="servicio-desc">{{ $servicio->descripcion }}</p>
                <div class="servicio-meta">
                    <span class="meta-chip">
                        <i class="bi bi-clock"></i> {{ $servicio->duracion }}
                    </span>
                    <span class="meta-chip precio">
                        <i class="bi bi-tag"></i> {{ $servicio->precio }}
                    </span>
                </div>
                <div class="servicio-footer">
                    <a href="{{ route('cuestionario', ['servicio_id' => $servicio->id]) }}"
                       class="btn-salmon" style="font-size:.85rem;padding:.5rem 1.2rem">
                        <i class="bi bi-heart"></i> Lo quiero
                    </a>
                </div>
            </div>
        </article>
        @empty
        <div style="grid-column:1/-1;text-align:center;padding:3rem;color:#999">
            <i class="bi bi-inbox" style="font-size:3rem;display:block;margin-bottom:1rem"></i>
            No hay servicios disponibles en esta categoría.
        </div>
        @endforelse
    </div>
</div>

@endsection

@push('scripts')
<script>
// Imágenes que ya estaban en caché antes de que se ejecutara el onload
document.querySelectorAll('.servicio-img img').forEach(img => {
    if (img.complete && img.naturalWidth) img.parentElement.classList.add('is-loaded');
});
</script>
@endpush
