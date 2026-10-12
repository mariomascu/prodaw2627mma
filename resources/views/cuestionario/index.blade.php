@extends('layouts.yoguifit')
@section('title', __('Cuéntanos sobre ti') . ' — YoguiFit')

@push('styles')
<style>
    .cuestionario-wrap {
        max-width: 680px;
        margin: 0 auto;
        padding: 3rem 1.25rem;
    }

    /* Pasos */
    .steps-bar {
        display: flex; align-items: center; justify-content: center;
        gap: .5rem; margin-bottom: 2.5rem;
    }
    .step {
        width: 36px; height: 36px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: .85rem; font-weight: 600;
        background: var(--selected-bg); color: var(--primary-alt);
        transition: all .3s;
        flex-shrink: 0;
    }
    .step.active { background: var(--primary); color: #fff; }
    .step.done { background: var(--terciario); color: var(--primary-dark); }
    .step-line { height: 2px; flex: 1; background: var(--selected-bg); max-width: 60px; }
    .step-line.done { background: var(--terciario); }

    /* Panel */
    .panel {
        display: none;
        background: #fff; border-radius: 16px;
        padding: 2rem 1.75rem;
        box-shadow: 0 4px 20px rgba(52,130,137,.12);
        animation: fadeIn .3s ease;
    }
    .panel.active { display: block; }
    @keyframes fadeIn { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:none} }

    .panel h2 {
        font-family: 'Montez', cursive; font-size: 1.8rem;
        color: var(--primary-dark); margin-bottom: .4rem;
    }
    .panel .panel-desc { font-size: .9rem; color: #777; margin-bottom: 1.75rem; line-height: 1.5; }

    /* Opciones género */
    .gender-options { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
    .gender-btn {
        background: var(--selected-bg); border: 3px solid transparent;
        border-radius: 14px; padding: 1.75rem 2.5rem;
        cursor: pointer; transition: all .2s;
        display: flex; flex-direction: column; align-items: center; gap: .7rem;
        font-family: 'Geologica', sans-serif;
        font-size: .95rem; color: var(--primary-dark);
        min-width: 140px;
    }
    .gender-btn i { font-size: 3rem; color: var(--primary); }
    .gender-btn:hover, .gender-btn.selected {
        border-color: var(--primary);
        background: rgba(52,130,137,.12);
    }
    .gender-btn.selected i { color: var(--salmon); }

    /* Opciones múltiple */
    .mc-options { display: flex; flex-direction: column; gap: .75rem; }
    .mc-btn {
        background: var(--selected-bg); border: 2px solid transparent;
        border-radius: 10px; padding: .9rem 1.2rem;
        cursor: pointer; transition: all .2s;
        font-family: 'Geologica', sans-serif; font-size: .92rem;
        color: var(--primary-dark); text-align: left;
        display: flex; align-items: center; gap: .6rem;
    }
    .mc-btn:hover, .mc-btn.selected {
        border-color: var(--primary); background: rgba(52,130,137,.1);
    }
    .mc-btn .mc-icon {
        width: 32px; height: 32px; border-radius: 50%;
        background: var(--primary); color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: .8rem; font-weight: 700; flex-shrink: 0;
    }

    /* Formulario datos */
    .form-group { margin-bottom: 1.25rem; }
    .form-group label { display: block; font-size: .88rem; font-weight: 500; color: var(--primary-dark); margin-bottom: .4rem; }
    .form-group input, .form-group select {
        width: 100%; padding: .75rem 1rem;
        border: 2px solid var(--selected-bg); border-radius: 10px;
        font-family: 'Geologica', sans-serif; font-size: .95rem;
        color: #333; background: #fff;
        transition: border-color .2s;
        outline: none;
    }
    .form-group input:focus, .form-group select:focus { border-color: var(--primary); }
    .form-group .error { font-size: .8rem; color: #d44; margin-top: .25rem; }

    /* Navegación pasos */
    .step-nav {
        display: flex; justify-content: space-between; align-items: center;
        margin-top: 2rem; gap: 1rem;
    }

    /* Servicio seleccionado */
    .servicio-seleccionado {
        background: var(--selected-bg); border-radius: 10px;
        padding: .75rem 1rem; margin-bottom: 1.5rem;
        display: flex; align-items: center; gap: .6rem;
        font-size: .9rem; color: var(--primary-dark);
    }
    .servicio-seleccionado i { color: var(--salmon); }
    .servicio-seleccionado strong { font-weight: 600; }

    .btn-primary-yf:disabled { opacity: .45; cursor: not-allowed; transform: none; }
    .aviso-paso { font-size: .82rem; color: #999; margin-top: 1rem; text-align: center; }

    @media (max-width: 768px) {
        .cuestionario-wrap { padding: 2rem 1rem; }
        .steps-bar { margin-bottom: 1.5rem; }
        .panel { padding: 1.5rem 1.25rem; }
        .gender-options { gap: .75rem; }
        .gender-btn { padding: 1.25rem 1rem; min-width: 0; flex: 1; }
        .gender-btn i { font-size: 2.4rem; }
    }
</style>
@endpush

@section('content')

<div class="page-banner">
    <h1><i class="bi bi-person-check"></i> {{ __('Cuéntanos sobre ti') }}</h1>
    <p>{{ __('Solo te lo preguntamos una vez: así podemos ofrecerte el tratamiento más adecuado') }}</p>
</div>

<div class="cuestionario-wrap">

    @if($errors->any())
        <div class="alert alert-error"><i class="bi bi-exclamation-circle"></i> {{ $errors->first() }}</div>
    @endif

    <!-- Barra de progreso -->
    <div class="steps-bar">
        <div class="step active" id="step-1">1</div>
        <div class="step-line" id="line-1"></div>
        <div class="step" id="step-2">2</div>
        <div class="step-line" id="line-2"></div>
        <div class="step" id="step-3">3</div>
        <div class="step-line" id="line-3"></div>
        <div class="step" id="step-4">4</div>
    </div>

    <form method="POST" action="{{ route('cuestionario.store') }}">
        @csrf

        <!-- Paso 1: Sexo -->
        <div class="panel active" id="panel-1">
            <h2>{{ __('¿Cuál es tu sexo?') }}</h2>
            <p class="panel-desc">{{ __('Ayúdanos a personalizar tu experiencia y recomendarte el tratamiento más adecuado.') }}</p>
            <input type="hidden" name="sexo" id="input-1" value="{{ old('sexo') }}">
            <div class="gender-options">
                @foreach($sexos as $valor => $texto)
                <button type="button" class="gender-btn opcion" data-paso="1" data-valor="{{ $valor }}">
                    <i class="bi bi-gender-{{ $valor === 'masculino' ? 'male' : 'female' }}"></i>
                    {{ __($texto) }}
                </button>
                @endforeach
            </div>
        </div>

        <!-- Paso 2: Objetivo -->
        <div class="panel" id="panel-2">
            <h2>{{ __('¿Cuál es tu objetivo?') }}</h2>
            <p class="panel-desc">{{ __('Selecciona la opción que mejor describa tu motivación principal.') }}</p>
            <input type="hidden" name="objetivo" id="input-2" value="{{ old('objetivo') }}">
            <div class="mc-options">
                @foreach($objetivos as $valor => $texto)
                <button type="button" class="mc-btn opcion" data-paso="2" data-valor="{{ $valor }}">
                    <div class="mc-icon">{{ chr(65 + $loop->index) }}</div>
                    {{ __($texto) }}
                </button>
                @endforeach
            </div>
        </div>

        <!-- Paso 3: Frecuencia -->
        <div class="panel" id="panel-3">
            <h2>{{ __('¿Con qué frecuencia?') }}</h2>
            <p class="panel-desc">{{ __('¿Con qué frecuencia te gustaría recibir tratamiento?') }}</p>
            <input type="hidden" name="frecuencia" id="input-3" value="{{ old('frecuencia') }}">
            <div class="mc-options">
                @foreach($frecuencias as $valor => $texto)
                <button type="button" class="mc-btn opcion" data-paso="3" data-valor="{{ $valor }}">
                    <div class="mc-icon">{{ chr(65 + $loop->index) }}</div>
                    {{ __($texto) }}
                </button>
                @endforeach
            </div>
        </div>

        <!-- Paso 4: Teléfono -->
        <div class="panel" id="panel-4">
            <h2>{{ __('Tu teléfono de contacto') }}</h2>
            <p class="panel-desc">{{ __('Lo usaremos para confirmar tus reservas por teléfono o WhatsApp.') }}</p>
            <div class="form-group">
                <label for="telefono"><i class="bi bi-telephone"></i> {{ __('Teléfono') }}</label>
                <input type="tel" id="telefono" name="telefono" placeholder="600 000 000"
                       value="{{ old('telefono', auth()->user()->telefono) }}" required>
            </div>
        </div>

        <!-- Navegación -->
        <div class="step-nav">
            <button type="button" class="btn-outline-yf" id="btnPrev" style="display:none">
                <i class="bi bi-arrow-left"></i> {{ __('Anterior') }}
            </button>
            <div></div>
            <button type="button" class="btn-primary-yf" id="btnNext" disabled>
                {{ __('Siguiente') }} <i class="bi bi-arrow-right"></i>
            </button>
            <button type="submit" class="btn-salmon" id="btnSubmit" style="display:none">
                <i class="bi bi-check-lg"></i> {{ __('Terminar') }}
            </button>
        </div>
        <p class="aviso-paso" id="avisoPaso">{{ __('Elige una opción para continuar') }}</p>
    </form>
</div>

@endsection

@push('scripts')
<script>
let pasoActual = 1;
const totalPasos = 4;

// En los pasos 1 a 3 hay que elegir una opción; el 4 es el teléfono
function pasoRespondido() {
    if (pasoActual === totalPasos) return true;
    return document.getElementById('input-' + pasoActual).value !== '';
}

function actualizar() {
    for (let i = 1; i <= totalPasos; i++) {
        document.getElementById('panel-' + i).classList.toggle('active', i === pasoActual);
        document.getElementById('step-' + i).classList.toggle('active', i === pasoActual);
        document.getElementById('step-' + i).classList.toggle('done', i < pasoActual);
        if (i < totalPasos) {
            document.getElementById('line-' + i).classList.toggle('done', i < pasoActual);
        }
    }
    document.getElementById('btnPrev').style.display   = pasoActual > 1 ? 'inline-flex' : 'none';
    document.getElementById('btnNext').style.display   = pasoActual < totalPasos ? 'inline-flex' : 'none';
    document.getElementById('btnSubmit').style.display = pasoActual === totalPasos ? 'inline-flex' : 'none';

    // "Siguiente" no se puede pulsar hasta elegir una opción
    document.getElementById('btnNext').disabled = ! pasoRespondido();
    document.getElementById('avisoPaso').style.display = pasoRespondido() ? 'none' : 'block';
}

// Al pulsar una opción se marca y se guarda su valor en el campo oculto del paso
document.querySelectorAll('.opcion').forEach(boton => {
    boton.addEventListener('click', () => {
        const paso = boton.dataset.paso;
        document.querySelectorAll('.opcion[data-paso="' + paso + '"]').forEach(b => b.classList.remove('selected'));
        boton.classList.add('selected');
        document.getElementById('input-' + paso).value = boton.dataset.valor;
        actualizar();
    });

    // Si el formulario vuelve con errores, se marcan las opciones que ya había elegido
    if (document.getElementById('input-' + boton.dataset.paso).value === boton.dataset.valor) {
        boton.classList.add('selected');
    }
});

document.getElementById('btnNext').addEventListener('click', () => {
    if (pasoRespondido() && pasoActual < totalPasos) {
        pasoActual++;
        actualizar();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
});

document.getElementById('btnPrev').addEventListener('click', () => {
    if (pasoActual > 1) {
        pasoActual--;
        actualizar();
    }
});

actualizar();
</script>
@endpush
