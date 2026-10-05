@extends('layouts.yoguifit')
@section('title', 'Tarifas — YoguiFit')

@push('styles')
<style>
    .tarifas-section { padding: 2.5rem 0 4rem; }

    .seccion-titulo {
        display: flex; align-items: center; gap: .75rem;
        font-family: 'Montez', cursive; font-size: 2rem;
        color: var(--primary-dark); margin: 2.5rem 0 1.2rem;
        padding-bottom: .5rem;
        border-bottom: 3px solid var(--primary);
    }
    .seccion-titulo i { color: var(--primary); font-size: 1.8rem; }

    .tarifa-card {
        background: #fff; border-radius: 14px;
        overflow: hidden; margin-bottom: 1rem;
        box-shadow: 0 2px 10px rgba(52,130,137,.08);
        border: 1px solid var(--selected-bg);
    }
    .tarifa-header {
        padding: 1.1rem 1.4rem;
        display: flex; align-items: center; justify-content: space-between;
        cursor: pointer;
        user-select: none;
        transition: background .18s;
        gap: 1rem;
    }
    .tarifa-header:hover { background: var(--selected-bg); }
    .tarifa-header.open { background: var(--selected-bg); }

    .tarifa-titulo-wrap { display: flex; align-items: center; gap: .75rem; flex: 1; }
    .tarifa-titulo { font-size: 1rem; font-weight: 600; color: var(--primary-dark); }

    .tarifa-chips { display: flex; gap: .5rem; flex-wrap: wrap; }
    .chip {
        display: inline-flex; align-items: center; gap: .25rem;
        padding: .25rem .75rem; border-radius: 20px; font-size: .78rem; font-weight: 500;
    }
    .chip-dur { background: var(--selected-bg); color: var(--primary-dark); }
    .chip-price { background: var(--salmon); color: #fff; }

    .tarifa-toggle {
        color: var(--primary); font-size: 1.2rem;
        transition: transform .3s;
    }
    .tarifa-header.open .tarifa-toggle { transform: rotate(180deg); }

    .tarifa-body {
        display: none; padding: 1rem 1.4rem 1.4rem;
        border-top: 1px solid var(--selected-bg);
    }
    .tarifa-body.open { display: block; }
    .tarifa-body p { font-size: .9rem; color: #555; line-height: 1.65; margin-bottom: 1rem; }
    .tarifa-body a {
        display: inline-flex; align-items: center; gap: .4rem;
        font-size: .85rem;
    }
</style>
@endpush

@section('content')

<div class="page-banner">
    <h1><i class="bi bi-tag"></i> Tarifas</h1>
    <p>Todos nuestros servicios con precios claros y transparentes</p>
</div>

<div class="container tarifas-section">

    <h2 class="seccion-titulo"><i class="bi bi-mortarboard-fill"></i> Cursos</h2>
    @foreach($cursos as $s)
    <div class="tarifa-card">
        <div class="tarifa-header" onclick="toggleTarifa(this)">
            <div class="tarifa-titulo-wrap">
                <span class="tarifa-titulo">{{ $s->titulo }}</span>
            </div>
            <div class="tarifa-chips">
                <span class="chip chip-dur"><i class="bi bi-clock"></i> {{ $s->duracion }}</span>
                <span class="chip chip-price"><i class="bi bi-tag"></i> {{ $s->precio }}</span>
            </div>
            <i class="bi bi-chevron-down tarifa-toggle"></i>
        </div>
        <div class="tarifa-body">
            <p>{{ $s->descripcion }}</p>
            <a href="{{ route('cuestionario', ['servicio_id' => $s->id]) }}" class="btn-salmon">
                <i class="bi bi-heart"></i> Lo quiero
            </a>
        </div>
    </div>
    @endforeach

    <h2 class="seccion-titulo"><i class="bi bi-hand-index-thumb-fill"></i> Masajes</h2>
    @foreach($masajes as $s)
    <div class="tarifa-card">
        <div class="tarifa-header" onclick="toggleTarifa(this)">
            <div class="tarifa-titulo-wrap">
                <span class="tarifa-titulo">{{ $s->titulo }}</span>
            </div>
            <div class="tarifa-chips">
                <span class="chip chip-dur"><i class="bi bi-clock"></i> {{ $s->duracion }}</span>
                <span class="chip chip-price"><i class="bi bi-tag"></i> {{ $s->precio }}</span>
            </div>
            <i class="bi bi-chevron-down tarifa-toggle"></i>
        </div>
        <div class="tarifa-body">
            <p>{{ $s->descripcion }}</p>
            <a href="{{ route('cuestionario', ['servicio_id' => $s->id]) }}" class="btn-salmon">
                <i class="bi bi-heart"></i> Lo quiero
            </a>
        </div>
    </div>
    @endforeach

    <h2 class="seccion-titulo"><i class="bi bi-flower1"></i> Yoga</h2>
    @foreach($yoga as $s)
    <div class="tarifa-card">
        <div class="tarifa-header" onclick="toggleTarifa(this)">
            <div class="tarifa-titulo-wrap">
                <span class="tarifa-titulo">{{ $s->titulo }}</span>
            </div>
            <div class="tarifa-chips">
                <span class="chip chip-dur"><i class="bi bi-clock"></i> {{ $s->duracion }}</span>
                <span class="chip chip-price"><i class="bi bi-tag"></i> {{ $s->precio }}</span>
            </div>
            <i class="bi bi-chevron-down tarifa-toggle"></i>
        </div>
        <div class="tarifa-body">
            <p>{{ $s->descripcion }}</p>
            <a href="{{ route('cuestionario', ['servicio_id' => $s->id]) }}" class="btn-salmon">
                <i class="bi bi-heart"></i> Lo quiero
            </a>
        </div>
    </div>
    @endforeach

</div>

@endsection

@push('scripts')
<script>
function toggleTarifa(header) {
    const card = header.closest('.tarifa-card');
    const body = card.querySelector('.tarifa-body');
    const isOpen = header.classList.contains('open');

    // Cerrar todos
    document.querySelectorAll('.tarifa-header.open').forEach(h => {
        h.classList.remove('open');
        h.closest('.tarifa-card').querySelector('.tarifa-body').classList.remove('open');
    });

    // Abrir si estaba cerrado
    if (!isOpen) {
        header.classList.add('open');
        body.classList.add('open');
    }
}
</script>
@endpush
