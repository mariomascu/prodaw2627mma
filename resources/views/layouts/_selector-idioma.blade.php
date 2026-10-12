{{-- Selector de idioma del header (desplegable sin JavaScript) --}}
@php $idiomas = \App\Models\Ajuste::idiomasActivos(); @endphp
@if(count($idiomas) > 1)
<details class="idioma-selector">
    <summary aria-label="{{ __('Idioma') }}">
        <img src="{{ \App\Models\Ajuste::bandera(app()->getLocale()) }}" alt="" class="bandera"> {{ Str::upper(Str::substr(app()->getLocale(), 0, 2)) }}
    </summary>
    <div class="idioma-lista">
        @foreach($idiomas as $codigo => $nombre)
            <a href="{{ route('idioma', $codigo) }}" class="{{ app()->getLocale() === $codigo ? 'activo' : '' }}">
                <img src="{{ \App\Models\Ajuste::bandera($codigo) }}" alt="" class="bandera"> {{ $nombre }}
                @if(app()->getLocale() === $codigo) <i class="bi bi-check-lg"></i> @endif
            </a>
        @endforeach
    </div>
</details>
@endif
