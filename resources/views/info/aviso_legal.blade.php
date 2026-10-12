@extends('layouts.yoguifit')
@section('title', __('Aviso legal') . ' — YoguiFit')
@section('content')
<div class="page-banner">
    <h1><i class="bi bi-file-earmark-text"></i> {{ __('Aviso legal') }}</h1>
</div>
<div class="container section" style="max-width:760px">
    <div class="card-yf card-pad">
        {{-- El texto está en resources/views/info/textos/<idioma>/aviso_legal.blade.php --}}
        @includeFirst(['info.textos.' . app()->getLocale() . '.aviso_legal', 'info.textos.es.aviso_legal'])
    </div>
    @if(app()->getLocale() !== 'es')
        <p style="font-size:.8rem;color:#999;text-align:center;margin-top:1rem">{{ __('Traducción orientativa: en caso de duda prevalece la versión en español.') }}</p>
    @endif
</div>
@endsection
