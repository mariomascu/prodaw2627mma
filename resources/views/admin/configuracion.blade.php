@extends('layouts.yoguifit')
@section('title', __('Configuración'))

@push('styles')
<style>
    .pestanas { display: flex; gap: .25rem; border-bottom: 2px solid var(--selected-bg); margin-bottom: 1.5rem; overflow-x: auto; }
    .pestanas a {
        padding: .75rem 1.25rem; text-decoration: none; color: #777; font-size: .92rem; white-space: nowrap;
        border-bottom: 3px solid transparent; margin-bottom: -2px; display: flex; align-items: center; gap: .4rem;
    }
    .pestanas a:hover { color: var(--primary); }
    .pestanas a.activa { color: var(--primary-dark); font-weight: 600; border-bottom-color: var(--primary); }

    .version-item { border-left: 3px solid var(--selected-bg); padding: .25rem 0 .9rem 1.1rem; position: relative; }
    .version-item::before {
        content: ''; position: absolute; left: -7px; top: .45rem; width: 11px; height: 11px;
        border-radius: 50%; background: var(--terciario);
    }
    .version-item.actual { border-left-color: var(--primary); }
    .version-item.actual::before { background: var(--salmon); }
    .version-cabecera { display: flex; align-items: center; gap: .6rem; flex-wrap: wrap; margin-bottom: .35rem; }
    .version-numero { font-weight: 600; color: var(--primary-dark); font-size: 1.05rem; }
    .version-fecha { color: #999; font-size: .82rem; }
    .version-item ul { margin-left: 1.1rem; color: #555; font-size: .9rem; line-height: 1.6; }
    .etiqueta-actual { background: var(--salmon); color: #fff; font-size: .7rem; padding: .1rem .55rem; border-radius: 10px; text-transform: uppercase; letter-spacing: .04em; }

    .casillas { display: flex; flex-direction: column; gap: .5rem; }
    .casillas label { display: flex; align-items: center; gap: .5rem; font-weight: 400; color: #444; }
    .casillas input { accent-color: var(--primary); width: 1rem; height: 1rem; }
    .tabla-horario input[type=time] {
        padding: .4rem .5rem; border: 2px solid var(--selected-bg); border-radius: 8px;
        font-family: 'Geologica', sans-serif; font-size: .88rem; color: #333;
    }
    .tabla-horario input[type=time]:focus { border-color: var(--primary); outline: none; }
        .rejilla-2 { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(260px, 100%), 1fr)); gap: 0 1rem; }
</style>
@endpush

@section('content')
@include('admin._menu')

<div class="container admin-wrap" style="max-width:900px">

    <nav class="pestanas">
        <a href="{{ route('admin.configuracion', ['pestana' => 'versiones']) }}" class="{{ $pestana === 'versiones' ? 'activa' : '' }}"><i class="bi bi-git"></i> {{ __('Versiones') }}</a>
        <a href="{{ route('admin.configuracion', ['pestana' => 'idiomas']) }}" class="{{ $pestana === 'idiomas' ? 'activa' : '' }}"><i class="bi bi-translate"></i> {{ __('Idiomas') }}</a>
        <a href="{{ route('admin.configuracion', ['pestana' => 'centro']) }}" class="{{ $pestana === 'centro' ? 'activa' : '' }}"><i class="bi bi-shop"></i> {{ __('Datos del centro') }}</a>
        <a href="{{ route('admin.configuracion', ['pestana' => 'horario']) }}" class="{{ $pestana === 'horario' ? 'activa' : '' }}"><i class="bi bi-clock"></i> {{ __('Horario') }}</a>
    </nav>

    @if($errors->any())
        <div class="alert alert-error"><i class="bi bi-exclamation-circle"></i> {{ $errors->first() }}</div>
    @endif

    {{-- ──────── PESTAÑA VERSIONES ──────── --}}
    @if($pestana === 'versiones')
        <div class="caja">
            <h3><i class="bi bi-plus-circle"></i> {{ __('Registrar una versión') }}</h3>
            <form method="POST" action="{{ route('admin.configuracion.versiones.store') }}">
                @csrf
                <div class="rejilla-2">
                    <div class="campo">
                        <label for="numero">{{ __('Número de versión') }}</label>
                        <input type="text" id="numero" name="numero" value="{{ old('numero') }}" placeholder="0.0.12" required>
                    </div>
                    <div class="campo">
                        <label for="fecha">{{ __('Fecha') }}</label>
                        <input type="date" id="fecha" name="fecha" value="{{ old('fecha', now()->format('Y-m-d')) }}" required>
                    </div>
                </div>
                <div class="campo">
                    <label for="cambios">{{ __('Cambios') }}</label>
                    <textarea id="cambios" name="cambios" rows="4" required placeholder="{{ __('Un cambio por línea') }}">{{ old('cambios') }}</textarea>
                    <div class="ayuda">{{ __('Escribe un cambio por línea.') }}</div>
                </div>
                <button type="submit" class="btn-salmon"><i class="bi bi-check-lg"></i> {{ __('Guardar versión') }}</button>
            </form>
        </div>

        <div class="caja">
            <h3><i class="bi bi-clock-history"></i> {{ __('Historial de versiones') }}</h3>
            @forelse($versiones as $version)
                <div class="version-item {{ $loop->first ? 'actual' : '' }}">
                    <div class="version-cabecera">
                        <span class="version-numero">{{ $version->numero }}</span>
                        <span class="version-fecha">{{ $version->fecha->format('d/m/Y') }}</span>
                        @if($loop->first) <span class="etiqueta-actual">{{ __('Actual') }}</span> @endif
                        <form method="POST" action="{{ route('admin.configuracion.versiones.destroy', $version) }}" style="margin-left:auto"
                              onsubmit="return confirm(@js(__('¿Eliminar la versión :numero?', ['numero' => $version->numero])))">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-peligro" title="{{ __('Eliminar') }}"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                    <ul>
                        @foreach($version->listaCambios() as $cambio)
                            <li>{{ $cambio }}</li>
                        @endforeach
                    </ul>
                </div>
            @empty
                <p class="vacio">{{ __('Todavía no hay versiones registradas.') }}</p>
            @endforelse
        </div>
    @endif

    {{-- ──────── PESTAÑA IDIOMAS ──────── --}}
    @if($pestana === 'idiomas')
        @php $activos = array_keys(\App\Models\Ajuste::idiomasActivos()); @endphp
        <form method="POST" action="{{ route('admin.configuracion.idiomas') }}" class="caja">
            @csrf
            @method('PUT')
            <h3><i class="bi bi-translate"></i> {{ __('Idiomas de la web') }}</h3>

            <div class="campo">
                <label>{{ __('Idiomas disponibles en el selector') }}</label>
                <div class="casillas">
                    @foreach(\App\Models\Ajuste::IDIOMAS as $codigo => $nombre)
                        <label>
                            <input type="checkbox" name="idiomas_activos[]" value="{{ $codigo }}" @checked(in_array($codigo, old('idiomas_activos', $activos)))>
                            <img src="{{ \App\Models\Ajuste::bandera($codigo) }}" alt="" class="bandera"> {{ $nombre }}
                        </label>
                    @endforeach
                </div>
                <div class="ayuda">{{ __('Si solo queda un idioma activo, el selector no se muestra.') }}</div>
            </div>

            <div class="campo">
                <label for="idioma_por_defecto">{{ __('Idioma por defecto') }}</label>
                <select id="idioma_por_defecto" name="idioma_por_defecto">
                    @foreach(\App\Models\Ajuste::IDIOMAS as $codigo => $nombre)
                        <option value="{{ $codigo }}" @selected(old('idioma_por_defecto', \App\Models\Ajuste::valor('idioma_por_defecto')) === $codigo)>{{ $nombre }}</option>
                    @endforeach
                </select>
                <div class="ayuda">{{ __('Es el idioma que ve quien entra por primera vez.') }}</div>
            </div>

            <button type="submit" class="btn-salmon"><i class="bi bi-check-lg"></i> {{ __('Guardar cambios') }}</button>
        </form>
    @endif

    {{-- ──────── PESTAÑA HORARIO ──────── --}}
    @if($pestana === 'horario')
        @php $horario = \App\Models\Ajuste::horario(); @endphp
        <form method="POST" action="{{ route('admin.configuracion.horario') }}" class="caja">
            @csrf
            @method('PUT')
            <h3><i class="bi bi-clock"></i> {{ __('Horario del centro') }}</h3>
            <p style="color:#777;font-size:.88rem;margin-bottom:1.25rem">
                {{ __('Al abrir un mes en la agenda se crea una hora reservable por cada hora completa dentro de este horario (con cierre a las 14:00, la última es a las 13:00). Deja un tramo vacío si ese día cierra.') }}
            </p>
            <div class="tabla-scroll">
                <table class="tabla tabla-horario">
                    <thead>
                        <tr><th>{{ __('Día') }}</th><th colspan="2">{{ __('Mañana') }}</th><th colspan="2">{{ __('Tarde') }}</th></tr>
                    </thead>
                    <tbody>
                    @foreach(\App\Models\Ajuste::DIAS as $dia => $nombreDia)
                        <tr>
                            <td><strong>{{ __($nombreDia) }}</strong></td>
                            @foreach([0, 1] as $tramo)
                                <td><input type="time" name="horario[{{ $dia }}][{{ $tramo }}][apertura]" value="{{ old("horario.$dia.$tramo.apertura", $horario[$dia][$tramo][0] ?? '') }}" aria-label="{{ __('Apertura') }}"></td>
                                <td><input type="time" name="horario[{{ $dia }}][{{ $tramo }}][cierre]" value="{{ old("horario.$dia.$tramo.cierre", $horario[$dia][$tramo][1] ?? '') }}" aria-label="{{ __('Cierre') }}"></td>
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="ayuda" style="margin:.75rem 0 1.25rem">{{ __('Los cambios se aplican a los meses que abras después; los meses ya abiertos no se modifican.') }}</div>
            <button type="submit" class="btn-salmon"><i class="bi bi-check-lg"></i> {{ __('Guardar cambios') }}</button>
        </form>
    @endif

    {{-- ──────── PESTAÑA DATOS DEL CENTRO ──────── --}}
    @if($pestana === 'centro')
        <form method="POST" action="{{ route('admin.configuracion.centro') }}" class="caja">
            @csrf
            @method('PUT')
            <h3><i class="bi bi-shop"></i> {{ __('Datos del centro') }}</h3>
            <p style="color:#777;font-size:.88rem;margin-bottom:1.25rem">{{ __('Se muestran en el pie de página, en Contacto, en Dónde estamos y en los textos legales.') }}</p>

            <div class="campo">
                <label for="centro_nombre">{{ __('Nombre del centro') }}</label>
                <input type="text" id="centro_nombre" name="centro_nombre" value="{{ old('centro_nombre', \App\Models\Ajuste::valor('centro_nombre')) }}" required>
            </div>
            <div class="rejilla-2">
                <div class="campo">
                    <label for="centro_telefono">{{ __('Teléfono') }}</label>
                    <input type="text" id="centro_telefono" name="centro_telefono" value="{{ old('centro_telefono', \App\Models\Ajuste::valor('centro_telefono')) }}" required>
                </div>
                <div class="campo">
                    <label for="centro_email">{{ __('Correo electrónico') }}</label>
                    <input type="text" id="centro_email" name="centro_email" value="{{ old('centro_email', \App\Models\Ajuste::valor('centro_email')) }}" required>
                </div>
            </div>
            <div class="campo">
                <label for="centro_direccion">{{ __('Dirección') }}</label>
                <input type="text" id="centro_direccion" name="centro_direccion" value="{{ old('centro_direccion', \App\Models\Ajuste::valor('centro_direccion')) }}" required>
            </div>
            <div class="campo">
                <label for="centro_instagram">{{ __('Usuario de Instagram') }}</label>
                <input type="text" id="centro_instagram" name="centro_instagram" value="{{ old('centro_instagram', \App\Models\Ajuste::valor('centro_instagram')) }}" placeholder="yoguifit">
            </div>

            <button type="submit" class="btn-salmon"><i class="bi bi-check-lg"></i> {{ __('Guardar cambios') }}</button>
        </form>
    @endif
</div>
@endsection
