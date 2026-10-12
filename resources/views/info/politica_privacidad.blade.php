@extends('layouts.yoguifit')
@section('title', __('Política de privacidad') . ' — YoguiFit')
@section('content')
<div class="page-banner">
    <h1><i class="bi bi-shield-check"></i> {{ __('Política de privacidad') }}</h1>
</div>
<div class="container section" style="max-width:760px">
    <div class="card-yf card-pad">
        {{-- El texto está en resources/views/info/textos/<idioma>/politica_privacidad.blade.php --}}
        @includeFirst(['info.textos.' . app()->getLocale() . '.politica_privacidad', 'info.textos.es.politica_privacidad'])
    </div>
    @if(app()->getLocale() !== 'es')
        <p style="font-size:.8rem;color:#999;text-align:center;margin-top:1rem">{{ __('Traducción orientativa: en caso de duda prevalece la versión en español.') }}</p>
    @endif
</div>
@endsection
