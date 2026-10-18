@extends('layouts.yoguifit')
@section('title', $cliente->name . ' — YoguiFit')

@section('content')
@include('admin._menu')

<div class="container admin-wrap">
    <div class="admin-titulo">
        <h2><i class="bi bi-person-circle" style="color:var(--primary)"></i> {{ $cliente->name }}</h2>
        <a href="{{ route('admin.clientes.index') }}" class="btn-outline-yf btn-peq"><i class="bi bi-arrow-left"></i> {{ __('Volver') }}</a>
    </div>

    <div class="caja">
        <h3><i class="bi bi-person-vcard"></i> {{ __('Datos del cliente') }}</h3>
        <table class="tabla">
            @php $sesiones = $cliente->sesionesReservadas(); $nivel = $cliente->nivel($sesiones); @endphp
            <tr><th>{{ __('Nivel') }}</th><td>@include('partials.insignia', ['nivel' => $nivel]) {{ $nivel ? '' : '—' }} <span style="color:#888;margin-left:.4rem">{{ __('Sesiones reservadas: :numero', ['numero' => $sesiones]) }}</span></td></tr>
            <tr><th>{{ __('Correo electrónico') }}</th><td>{{ $cliente->email }}</td></tr>
            <tr><th>{{ __('Teléfono') }}</th><td>{{ $cliente->telefono ?? '—' }}</td></tr>
            <tr><th>{{ __('Sexo') }}</th><td>{{ __(\App\Models\User::SEXOS[$cliente->sexo] ?? '—') }}</td></tr>
            <tr><th>{{ __('Objetivo') }}</th><td>{{ __(\App\Models\User::OBJETIVOS[$cliente->objetivo] ?? '—') }}</td></tr>
            <tr><th>{{ __('Frecuencia') }}</th><td>{{ __(\App\Models\User::FRECUENCIAS[$cliente->frecuencia] ?? '—') }}</td></tr>
            <tr><th>{{ __('Registrado') }}</th><td>{{ $cliente->created_at?->format('d/m/Y H:i') }}</td></tr>
            <tr><th>{{ __('Cuestionario') }}</th><td>{{ $cliente->perfil_completado_at ? __('Respondido el :fecha', ['fecha' => $cliente->perfil_completado_at->format('d/m/Y')]) : __('Sin responder') }}</td></tr>
        </table>
    </div>

    @if($bonos->isNotEmpty())
        <div class="caja">
            <h3><i class="bi bi-ticket-perforated"></i> {{ __('Bonos de yoga') }}</h3>
            <table class="tabla">
                @foreach($bonos as $bono)
                    <tr>
                        <th>{{ $bono->servicio->titulo ?? __('Bono de yoga') }}</th>
                        <td>{{ __('Quedan :restantes de :total sesiones', ['restantes' => $bono->sesionesRestantes(), 'total' => $bono->sesiones_totales]) }}</td>
                        <td style="color:#888">{{ $bono->created_at->format('d/m/Y') }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endif

    <div class="caja">
        <h3><i class="bi bi-calendar-check"></i> Citas ({{ $citas->count() }})</h3>
        <div class="tabla-scroll">
            <table class="tabla">
                <thead>
                    <tr><th>{{ __('Día y hora') }}</th><th>{{ __('Servicio') }}</th><th>{{ __('Precio') }}</th><th>{{ __('Comentarios') }}</th><th>{{ __('Estado') }}</th></tr>
                </thead>
                <tbody>
                @forelse($citas as $cita)
                    <tr>
                        <td style="white-space:nowrap">
                            @if($cita->inicio)
                                {{ $cita->inicio->format('d/m/Y H:i') }}
                            @else
                                <span style="color:#999">{{ __('Solicitud del :fecha', ['fecha' => $cita->created_at->format('d/m/Y')]) }}</span>
                            @endif
                            @if($cita->solicitudPendiente)
                                <div style="font-size:.78rem;color:#8a5a00"><i class="bi bi-arrow-left-right"></i> {{ __('Pide cambio a :fecha', ['fecha' => $cita->solicitudPendiente->inicio->format('d/m H:i')]) }}</div>
                            @endif
                        </td>
                        <td>{{ $cita->servicio->titulo ?? __('Sin servicio') }}</td>
                        <td style="white-space:nowrap">
                            {{ $cita->precio_final ?? ($cita->bono_id ? __('Bono') : '—') }}@if($cita->descuento) <small style="color:#888">(−{{ $cita->descuento }} %)</small>@endif
                            @if($cita->pago)
                                <div style="font-size:.78rem;color:{{ $cita->pago->estado === 'pagado' ? 'var(--primary)' : '#888' }}">
                                    <i class="bi bi-credit-card"></i> {{ __(ucfirst($cita->pago->estado)) }}
                                </div>
                            @endif
                        </td>
                        <td>{{ $cita->notas ?? '—' }}</td>
                        <td>
                            @if($cita->estado === 'cancelada')
                                {{-- Una cita cancelada ya ha liberado sus horas: no se puede reactivar --}}
                                <span class="badge badge-cancelada">{{ __('Cancelada') }}</span>
                            @else
                            {{-- Cambiar el estado de la cita (al cancelarla se liberan sus horas) --}}
                            <form method="POST" action="{{ route('admin.citas.update', $cita) }}" style="display:flex;gap:.4rem;align-items:center">
                                @csrf
                                @method('PATCH')
                                <select name="estado" class="estado">
                                    @foreach(['pendiente', 'confirmada', 'cancelada'] as $estado)
                                        <option value="{{ $estado }}" @selected($cita->estado === $estado)>{{ __(ucfirst($estado)) }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn-primary-yf btn-peq" title="{{ __('Guardar') }}"><i class="bi bi-check-lg"></i></button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="vacio">{{ __('Este cliente todavía no ha reservado nada.') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
