@extends('layouts.yoguifit')
@section('title', 'Reservar sesión — YoguiFit')

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
    <h1><i class="bi bi-calendar-check"></i> Reservar sesión</h1>
    <p>Para ofrecerte nuestra disponibilidad necesitamos que respondas honestamente a 4 cuestiones</p>
</div>

<div class="cuestionario-wrap">

    <!-- Barra de progreso -->
    <div class="steps-bar" id="stepsBar">
        <div class="step active" id="step-1">1</div>
        <div class="step-line" id="line-1"></div>
        <div class="step" id="step-2">2</div>
        <div class="step-line" id="line-2"></div>
        <div class="step" id="step-3">3</div>
        <div class="step-line" id="line-3"></div>
        <div class="step" id="step-4">4</div>
    </div>

    <form method="POST" action="{{ route('cuestionario.store') }}" id="reservaForm">
        @csrf
        @if($servicio)
            <input type="hidden" name="servicio_id" value="{{ $servicio->id }}">
        @endif

        <!-- Paso 1: Género -->
        <div class="panel active" id="panel-1">
            <h2>¿Cuál es tu sexo?</h2>
            <p class="panel-desc">Ayúdanos a personalizar tu experiencia y recomendarte el tratamiento más adecuado.</p>
            @if($servicio)
            <div class="servicio-seleccionado">
                <i class="bi bi-check-circle-fill"></i>
                Servicio seleccionado: <strong>{{ $servicio->titulo }}</strong> — {{ $servicio->precio }}
            </div>
            @endif
            <input type="hidden" name="sexo" id="sexoInput">
            <div class="gender-options">
                <button type="button" class="gender-btn" onclick="selectGender(this, 'masculino')">
                    <i class="bi bi-gender-male"></i>
                    Masculino
                </button>
                <button type="button" class="gender-btn" onclick="selectGender(this, 'femenino')">
                    <i class="bi bi-gender-female"></i>
                    Femenino
                </button>
            </div>
        </div>

        <!-- Paso 2 -->
        <div class="panel" id="panel-2">
            <h2>¿Cuál es tu objetivo?</h2>
            <p class="panel-desc">Selecciona la opción que mejor describa tu motivación principal.</p>
            <input type="hidden" name="respuesta_2" id="resp2Input">
            <div class="mc-options">
                @php $opts2 = ['Relajación y reducción del estrés','Alivio de dolor muscular o lesiones','Mejora del rendimiento deportivo','Bienestar general y salud integral'] @endphp
                @foreach($opts2 as $i => $opt)
                <button type="button" class="mc-btn" onclick="selectMC(this, 'resp2Input', '{{ $opt }}')">
                    <div class="mc-icon">{{ chr(65+$i) }}</div>
                    {{ $opt }}
                </button>
                @endforeach
            </div>
        </div>

        <!-- Paso 3 -->
        <div class="panel" id="panel-3">
            <h2>¿Con qué frecuencia?</h2>
            <p class="panel-desc">¿Con qué frecuencia te gustaría recibir tratamiento?</p>
            <input type="hidden" name="respuesta_3" id="resp3Input">
            <div class="mc-options">
                @php $opts3 = ['Primera vez, quiero probar','Una vez al mes','Cada dos semanas','Semanalmente'] @endphp
                @foreach($opts3 as $i => $opt)
                <button type="button" class="mc-btn" onclick="selectMC(this, 'resp3Input', '{{ $opt }}')">
                    <div class="mc-icon">{{ chr(65+$i) }}</div>
                    {{ $opt }}
                </button>
                @endforeach
            </div>
        </div>

        <!-- Paso 4: Datos personales -->
        <div class="panel" id="panel-4">
            <h2>Tus datos de contacto</h2>
            <p class="panel-desc">Necesitamos tus datos para confirmar la reserva y contactar contigo.</p>
            <div class="form-group">
                <label for="nombre"><i class="bi bi-person"></i> Nombre completo</label>
                <input type="text" id="nombre" name="nombre" placeholder="Tu nombre y apellidos"
                       value="{{ old('nombre') }}" required>
                @error('nombre') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="telefono"><i class="bi bi-telephone"></i> Teléfono</label>
                <input type="tel" id="telefono" name="telefono" placeholder="600 000 000"
                       value="{{ old('telefono') }}" required>
                @error('telefono') <div class="error">{{ $message }}</div> @enderror
            </div>
        </div>

        <!-- Navegación -->
        <div class="step-nav" id="stepNav">
            <button type="button" class="btn-outline-yf" id="btnPrev" onclick="prevStep()" style="display:none">
                <i class="bi bi-arrow-left"></i> Anterior
            </button>
            <div></div>
            <button type="button" class="btn-primary-yf" id="btnNext" onclick="nextStep()">
                Siguiente <i class="bi bi-arrow-right"></i>
            </button>
            <button type="submit" class="btn-salmon" id="btnSubmit" style="display:none">
                <i class="bi bi-send"></i> Enviar solicitud
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
let currentStep = 1;
const totalSteps = 4;

function updateUI() {
    for (let i = 1; i <= totalSteps; i++) {
        document.getElementById('panel-' + i).classList.toggle('active', i === currentStep);
        const stepEl = document.getElementById('step-' + i);
        stepEl.classList.toggle('active', i === currentStep);
        stepEl.classList.toggle('done', i < currentStep);
        if (i < totalSteps) {
            document.getElementById('line-' + i).classList.toggle('done', i < currentStep);
        }
    }
    document.getElementById('btnPrev').style.display = currentStep > 1 ? 'inline-flex' : 'none';
    document.getElementById('btnNext').style.display = currentStep < totalSteps ? 'inline-flex' : 'none';
    document.getElementById('btnSubmit').style.display = currentStep === totalSteps ? 'inline-flex' : 'none';
}

function nextStep() {
    if (currentStep < totalSteps) {
        currentStep++;
        updateUI();
        window.scrollTo({top: 0, behavior: 'smooth'});
    }
}

function prevStep() {
    if (currentStep > 1) {
        currentStep--;
        updateUI();
        window.scrollTo({top: 0, behavior: 'smooth'});
    }
}

function selectGender(btn, value) {
    document.querySelectorAll('.gender-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    document.getElementById('sexoInput').value = value;
    setTimeout(nextStep, 350);
}

function selectMC(btn, inputId, value) {
    btn.closest('.mc-options').querySelectorAll('.mc-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    document.getElementById(inputId).value = value;
    setTimeout(nextStep, 350);
}
</script>
@endpush
