@extends('layouts.yoguifit')
@section('title', __('Clientes') . ' — YoguiFit')

@push('styles')
<style>
    /* La columna del nombre es más ancha y no se parte en varias líneas */
    .tabla .col-nombre { min-width: 260px; white-space: nowrap; }
</style>
@endpush

@section('content')
@include('admin._menu')

<div class="container admin-wrap">
    <div class="admin-titulo">
        <h2>Clientes ({{ $clientes->total() }})</h2>
        <form method="GET" action="{{ route('admin.clientes.index') }}" style="display:flex;gap:.5rem">
            <div class="campo" style="margin:0">
                <input type="search" name="buscar" value="{{ $buscar }}" placeholder="{{ __('Nombre, email o teléfono') }}">
            </div>
            <button type="submit" class="btn-primary-yf btn-peq"><i class="bi bi-search"></i></button>
        </form>
    </div>

    <div class="caja">
        <div class="tabla-scroll">
            <table class="tabla">
                <thead>
                    <tr>
                        <th class="col-nombre">{{ __('Nombre') }}</th><th>{{ __('Nivel') }}</th><th>{{ __('Sesiones') }}</th><th>{{ __('Correo electrónico') }}</th><th>{{ __('Teléfono') }}</th>
                        <th>{{ __('Sexo') }}</th><th>{{ __('Objetivo') }}</th><th>{{ __('Frecuencia') }}</th><th>{{ __('Alta') }}</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($clientes as $cliente)
                    <tr>
                        <td class="col-nombre"><a href="{{ route('admin.clientes.show', $cliente) }}">{{ $cliente->name }}</a></td>
                        <td>@include('partials.insignia', ['nivel' => $cliente->nivel($cliente->sesiones_count)])</td>
                        <td>{{ $cliente->sesiones_count }}</td>
                        <td>{{ $cliente->email }}</td>
                        <td>{{ $cliente->telefono ?? '—' }}</td>
                        <td>{{ __(\App\Models\User::SEXOS[$cliente->sexo] ?? '—') }}</td>
                        <td>{{ __(\App\Models\User::OBJETIVOS[$cliente->objetivo] ?? '—') }}</td>
                        <td>{{ __(\App\Models\User::FRECUENCIAS[$cliente->frecuencia] ?? '—') }}</td>
                        <td>{{ $cliente->created_at?->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="vacio">{{ $buscar ? __('No hay clientes que coincidan con la búsqueda.') : __('Todavía no hay clientes.') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($clientes->hasPages())
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:1rem;font-size:.88rem">
                @if($clientes->onFirstPage())
                    <span></span>
                @else
                    <a href="{{ $clientes->previousPageUrl() }}" class="btn-outline-yf btn-peq"><i class="bi bi-arrow-left"></i> {{ __('Anteriores') }}</a>
                @endif
                <span style="color:#888">Página {{ $clientes->currentPage() }} de {{ $clientes->lastPage() }}</span>
                @if($clientes->hasMorePages())
                    <a href="{{ $clientes->nextPageUrl() }}" class="btn-outline-yf btn-peq">{{ __('Siguientes') }} <i class="bi bi-arrow-right"></i></a>
                @else
                    <span></span>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
