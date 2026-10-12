{{-- Formulario para crear y para editar un servicio --}}
@extends('layouts.yoguifit')
@section('title', ($servicio->exists ? __('Editar servicio') : __('Nuevo servicio')) . ' — YoguiFit')

@push('styles')
<style>
    .traduccion { border: 2px solid var(--selected-bg); border-radius: 10px; margin-bottom: .6rem; }
    .traduccion summary { cursor: pointer; padding: .7rem 1rem; display: flex; align-items: center; gap: .5rem; font-weight: 500; color: var(--primary-dark); }
    .traduccion[open] summary { border-bottom: 1px solid var(--selected-bg); }
    .traduccion-ok { color: var(--primary); margin-left: auto; }
    .traduccion-campos { padding: 1rem; }
    .rejilla-campos { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(200px, 100%), 1fr)); gap: 0 1rem; }
</style>
@endpush

@section('content')
@include('admin._menu')

<div class="container admin-wrap" style="max-width:760px">
    <div class="admin-titulo">
        <h2>{{ $servicio->exists ? __('Editar servicio') : __('Nuevo servicio') }}</h2>
        <a href="{{ route('admin.servicios.index') }}" class="btn-outline-yf btn-peq"><i class="bi bi-arrow-left"></i> {{ __('Volver') }}</a>
    </div>

    @if($errors->any())
        <div class="alert alert-error"><i class="bi bi-exclamation-circle"></i> {{ __('Revisa los campos marcados.') }}</div>
    @endif

    <form method="POST" enctype="multipart/form-data" class="caja"
          action="{{ $servicio->exists ? route('admin.servicios.update', $servicio) : route('admin.servicios.store') }}">
        @csrf
        @if($servicio->exists)
            @method('PUT')
        @endif

        <div class="campo">
            <label for="tipo">{{ __('Tipo de servicio') }}</label>
            <select id="tipo" name="tipo" required>
                @foreach(\App\Models\Servicio::TIPOS as $clave => $nombre)
                    <option value="{{ $clave }}" @selected(old('tipo', $servicio->tipo) === $clave)>{{ __($nombre) }}</option>
                @endforeach
            </select>
            @error('tipo') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="campo">
            <label for="titulo">{{ __('Título') }}</label>
            <input type="text" id="titulo" name="titulo" value="{{ old('titulo', $servicio->titulo) }}" required maxlength="150">
            @error('titulo') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="campo">
            <label for="descripcion">{{ __('Descripción') }}</label>
            <textarea id="descripcion" name="descripcion" rows="4" required maxlength="2000">{{ old('descripcion', $servicio->descripcion) }}</textarea>
            @error('descripcion') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(min(220px,100%),1fr));gap:0 1rem">
            <div class="campo">
                {{-- La etiqueta cambia según el tipo (ver script de abajo) --}}
                <label for="duracion" id="etiquetaDuracion">{{ __(\App\Models\Servicio::etiquetaDuracion(old('tipo', $servicio->tipo))) }}</label>
                <input type="text" id="duracion" name="duracion" value="{{ old('duracion', $servicio->duracion) }}" required maxlength="50">
                <div class="ayuda" id="ayudaDuracion"></div>
                @error('duracion') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="campo">
                <label for="precio">{{ __('Precio') }}</label>
                <input type="text" id="precio" name="precio" value="{{ old('precio', $servicio->precio) }}" required maxlength="50" placeholder="90 €">
                <div class="ayuda">{{ __('Tal como se mostrará: «90 €», «160 €/mes»…') }}</div>
                @error('precio') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="campo">
                <label for="orden">{{ __('Orden') }}</label>
                <input type="number" id="orden" name="orden" value="{{ old('orden', $servicio->orden ?? 0) }}" min="0">
                <div class="ayuda">{{ __('Posición dentro de su tipo (los números bajos van primero).') }}</div>
                @error('orden') <div class="error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="campo">
            <label for="imagen_archivo">{{ __('Imagen') }}</label>
            @if($servicio->imagen_url)
                <img src="{{ $servicio->imagen_url }}" alt="" style="width:100%;max-width:320px;height:180px;object-fit:cover;border-radius:10px;display:block;margin-bottom:.6rem">
            @endif
            <input type="file" id="imagen_archivo" name="imagen_archivo" accept="image/jpeg,image/png,image/webp">
            <div class="ayuda">JPG, PNG o WEBP de 2 MB como máximo. Se guarda en la base de datos.{{ $servicio->imagen_url ? ' Déjalo vacío para mantener la actual.' : '' }}</div>
            @error('imagen_archivo') <div class="error">{{ $message }}</div> @enderror
            @if($servicio->imagen_url)
                <label style="display:flex;align-items:center;gap:.4rem;font-weight:400;margin-top:.5rem;color:#666">
                    <input type="checkbox" name="quitar_imagen" value="1"> {{ __('Quitar la imagen actual') }}
                </label>
            @endif
        </div>

        {{-- Traducciones: un bloque desplegable por idioma --}}
        <div class="campo">
            <label>{{ __('Traducciones') }}</label>
            <div class="ayuda" style="margin-bottom:.6rem">{{ __('Opcionales. Si dejas un campo vacío, se muestra el texto en español.') }}</div>
            @foreach(\App\Models\Ajuste::IDIOMAS as $codigo => $nombreIdioma)
                @continue($codigo === 'es')
                @php $tr = $servicio->exists ? $servicio->traducciones->firstWhere('idioma', $codigo) : null; @endphp
                <details class="traduccion" @if($errors->has("traducciones.$codigo.*")) open @endif>
                    <summary><img src="{{ \App\Models\Ajuste::bandera($codigo) }}" alt="" class="bandera"> {{ $nombreIdioma }}
                        @if($tr) <span class="traduccion-ok"><i class="bi bi-check-circle-fill"></i></span> @endif
                    </summary>
                    <div class="traduccion-campos">
                        <div class="campo">
                            <label for="tr_{{ $codigo }}_titulo">{{ __('Título') }}</label>
                            <input type="text" id="tr_{{ $codigo }}_titulo" name="traducciones[{{ $codigo }}][titulo]" value="{{ old("traducciones.$codigo.titulo", $tr?->titulo) }}" maxlength="150">
                        </div>
                        <div class="campo">
                            <label for="tr_{{ $codigo }}_descripcion">{{ __('Descripción') }}</label>
                            <textarea id="tr_{{ $codigo }}_descripcion" name="traducciones[{{ $codigo }}][descripcion]" rows="3" maxlength="2000">{{ old("traducciones.$codigo.descripcion", $tr?->descripcion) }}</textarea>
                        </div>
                        <div class="rejilla-campos">
                            <div class="campo">
                                <label for="tr_{{ $codigo }}_duracion">{{ __('Duración / sesiones') }}</label>
                                <input type="text" id="tr_{{ $codigo }}_duracion" name="traducciones[{{ $codigo }}][duracion]" value="{{ old("traducciones.$codigo.duracion", $tr?->duracion) }}" maxlength="50">
                            </div>
                            <div class="campo">
                                <label for="tr_{{ $codigo }}_precio">{{ __('Precio') }}</label>
                                <input type="text" id="tr_{{ $codigo }}_precio" name="traducciones[{{ $codigo }}][precio]" value="{{ old("traducciones.$codigo.precio", $tr?->precio) }}" maxlength="50">
                            </div>
                        </div>
                    </div>
                </details>
            @endforeach
        </div>

        <button type="submit" class="btn-salmon"><i class="bi bi-check-lg"></i> {{ $servicio->exists ? __('Guardar cambios') : __('Crear servicio') }}</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
// Cambia la etiqueta y el ejemplo del campo "duración" según el tipo elegido
const textosDuracion = {
    cursos:  [@js(__('Duración del curso')), @js(__('Por ejemplo: «30 h» o «120 min».'))],
    masajes: [@js(__('Duración del servicio')), @js(__('Por ejemplo: «60 min».'))],
    yoga:    [@js(__('Sesiones al mes')), @js(__('Por ejemplo: «8 sesiones/mes».'))],
};
const selectorTipo = document.getElementById('tipo');

function actualizarDuracion() {
    const [etiqueta, ayuda] = textosDuracion[selectorTipo.value];
    document.getElementById('etiquetaDuracion').textContent = etiqueta;
    document.getElementById('ayudaDuracion').textContent = ayuda;
}

selectorTipo.addEventListener('change', actualizarDuracion);
actualizarDuracion();
</script>
@endpush
