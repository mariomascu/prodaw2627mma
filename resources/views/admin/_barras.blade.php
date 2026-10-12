{{-- Lista de barras horizontales. Recibe $datos = [etiqueta => número] --}}
@php $total = array_sum($datos); @endphp
@if($total === 0)
    <p class="vacio" style="padding:1rem">{{ __('Todavía no hay datos.') }}</p>
@else
    @foreach($datos as $etiqueta => $numero)
        @php $porcentaje = round($numero * 100 / $total); @endphp
        <div class="barra">
            <div class="barra-texto">
                <span>{{ __($etiqueta) }}</span>
                <strong>{{ $numero }} <small>({{ $porcentaje }}%)</small></strong>
            </div>
            <div class="barra-fondo"><div class="barra-relleno" style="width: {{ $porcentaje }}%"></div></div>
        </div>
    @endforeach
@endif
