@extends('layouts.yoguifit')
@section('title', __('Acerca de') . ' — YoguiFit')
@section('content')
<div class="page-banner">
    <h1><i class="bi bi-info-circle"></i> {{ __('Acerca de YoguiFit') }}</h1>
    <p>{{ __('Especialista en Terapia Deportiva y Masaje Thai Fusión') }}</p>
</div>
<div class="container section" style="max-width:760px">
    <div class="card-yf card-pad">
        {{-- El texto está en resources/views/info/textos/<idioma>/acerca_de.blade.php --}}
        @includeFirst(['info.textos.' . app()->getLocale() . '.acerca_de', 'info.textos.es.acerca_de'])
    </div>
</div>
@endsection
