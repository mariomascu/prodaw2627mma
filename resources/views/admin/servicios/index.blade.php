@extends('layouts.yoguifit')
@section('title', __('Servicios') . ' — YoguiFit')

@section('content')
@include('admin._menu')

<div class="container admin-wrap">
    <div class="admin-titulo">
        <h2>Servicios ({{ $servicios->count() }})</h2>
        <a href="{{ route('admin.servicios.create') }}" class="btn-salmon"><i class="bi bi-plus-lg"></i> {{ __('Nuevo servicio') }}</a>
    </div>

    {{-- Filtro por tipo --}}
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1rem">
        <a href="{{ route('admin.servicios.index') }}" class="{{ $tipo ? 'btn-outline-yf' : 'btn-primary-yf' }} btn-peq">{{ __('Todos') }}</a>
        @foreach(\App\Models\Servicio::TIPOS as $clave => $nombre)
            <a href="{{ route('admin.servicios.index', ['tipo' => $clave]) }}" class="{{ $tipo === $clave ? 'btn-primary-yf' : 'btn-outline-yf' }} btn-peq">{{ __($nombre) }}</a>
        @endforeach
    </div>

    <div class="caja">
        <div class="tabla-scroll">
            <table class="tabla">
                <thead>
                    <tr><th>{{ __('Imagen') }}</th><th>{{ __('Título') }}</th><th>{{ __('Tipo') }}</th><th>{{ __('Duración / sesiones') }}</th><th>{{ __('Precio') }}</th><th>{{ __('Orden') }}</th><th>{{ __('Citas') }}</th><th></th></tr>
                </thead>
                <tbody>
                @forelse($servicios as $servicio)
                    <tr>
                        <td>
                            @if($servicio->imagen_url)
                                <img src="{{ $servicio->imagen_url }}" alt="" style="width:64px;height:44px;object-fit:cover;border-radius:6px;display:block" loading="lazy">
                            @else
                                <span style="color:#bbb">—</span>
                            @endif
                        </td>
                        <td><strong>{{ $servicio->titulo }}</strong></td>
                        <td>{{ __(\App\Models\Servicio::TIPOS[$servicio->tipo] ?? $servicio->tipo) }}</td>
                        <td>{{ $servicio->duracion }}</td>
                        <td>{{ $servicio->precio }}</td>
                        <td>{{ $servicio->orden }}</td>
                        <td>{{ $servicio->citas_count }}</td>
                        <td style="white-space:nowrap">
                            <a href="{{ route('admin.servicios.edit', $servicio) }}" class="btn-outline-yf btn-peq"><i class="bi bi-pencil"></i> {{ __('Editar') }}</a>
                            <form method="POST" action="{{ route('admin.servicios.destroy', $servicio) }}" style="display:inline"
                                  onsubmit="return confirm(@js(__('¿Eliminar «:titulo»? Las citas ya hechas se conservan.', ['titulo' => $servicio->titulo])))">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-peligro"><i class="bi bi-trash"></i> {{ __('Eliminar') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="vacio">{{ __('No hay servicios.') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
