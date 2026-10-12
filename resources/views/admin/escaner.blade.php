@extends('layouts.yoguifit')
@section('title', __('Lector de QR') . ' — YoguiFit')

@push('styles')
<style>
    .escaner-wrap { max-width: 520px; margin: 0 auto; padding: 2rem 1rem 3rem; text-align: center; }
    #lector { width: 100%; border-radius: 16px; overflow: hidden; background: #0d2224; min-height: 280px; }
    #lector video { border-radius: 16px; }
    .escaner-estado { margin-top: 1rem; font-size: .92rem; color: #555; min-height: 1.5em; }
    .escaner-estado.error { color: #b33; }
</style>
@endpush

@section('content')
<div class="page-banner" style="padding-top:2rem;padding-bottom:1.75rem">
    <h1><i class="bi bi-qr-code-scan"></i> {{ __('Lector de QR') }}</h1>
    <p>{{ __('Apunta con la cámara al código QR de la cita del cliente') }}</p>
</div>

<div class="escaner-wrap">
    <div id="lector"></div>
    <p class="escaner-estado" id="estado">{{ __('Abriendo la cámara…') }}</p>
    <button type="button" class="btn-outline-yf" id="reintentar" hidden><i class="bi bi-arrow-clockwise"></i> {{ __('Volver a intentarlo') }}</button>
</div>
@endsection

@push('scripts')
{{-- Lector de códigos QR con la cámara (https://github.com/mebjas/html5-qrcode) --}}
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
(function () {
    const estado = document.getElementById('estado');
    const reintentar = document.getElementById('reintentar');
    // Solo se aceptan QR que apunten a la validación de citas de esta web
    const prefijoValido = @js(url('/admin/citas')) + '/';
    const textos = {
        apunta: @js(__('Apunta al código QR.')),
        leido: @js(__('Código leído. Abriendo la cita…')),
        ajeno: @js(__('Este código QR no es de una cita de YoguiFit.')),
        sinCamara: @js(__('No se ha podido abrir la cámara. Revisa los permisos del navegador.')),
    };
    let lector = null;

    function mostrar(texto, esError = false) {
        estado.textContent = texto;
        estado.classList.toggle('error', esError);
    }

    function iniciar() {
        reintentar.hidden = true;
        lector = new Html5Qrcode('lector');
        lector.start(
            { facingMode: 'environment' },            // cámara trasera del móvil
            { fps: 10, qrbox: { width: 240, height: 240 } },
            texto => {
                if (texto.startsWith(prefijoValido)) {
                    mostrar(textos.leido);
                    lector.stop().finally(() => { window.location.href = texto; });
                } else {
                    mostrar(textos.ajeno, true);
                }
            },
            () => {}                                    // fotogramas sin código: no hacer nada
        ).then(() => mostrar(textos.apunta))
         .catch(() => { mostrar(textos.sinCamara, true); reintentar.hidden = false; });
    }

    reintentar.addEventListener('click', iniciar);
    iniciar();
})();
</script>
@endpush
