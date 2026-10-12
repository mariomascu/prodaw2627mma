@extends('layouts.yoguifit')
@section('title', __('Términos y condiciones') . ' — YoguiFit')
@section('content')
<div class="page-banner">
    <h1><i class="bi bi-file-earmark-check"></i> {{ __('Términos y condiciones') }}</h1>
</div>
<div class="container section" style="max-width:760px">
    <div class="card-yf card-pad">
        {{-- El texto está en resources/views/info/textos/<idioma>/terminos_condiciones.blade.php --}}
        @includeFirst(['info.textos.' . app()->getLocale() . '.terminos_condiciones', 'info.textos.es.terminos_condiciones'])
    </div>
    @if(app()->getLocale() !== 'es')
        <p style="font-size:.8rem;color:#999;text-align:center;margin-top:1rem">{{ __('Traducción orientativa: en caso de duda prevalece la versión en español.') }}</p>
    @endif
</div>
@endsection
