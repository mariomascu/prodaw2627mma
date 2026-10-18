@extends('emails.layout')

@section('contenido')
    <h1 style="font-size:22px;color:#123437;margin:0 0 12px">{{ __('¡Hola, :nombre!', ['nombre' => $user->name]) }}</h1>
    <p style="margin:0 0 14px">{{ __('Gracias por crear tu cuenta en YoguiFit. Ya puedes reservar tus sesiones de masaje y de yoga desde la web.') }}</p>

    <p style="margin:0 0 8px;font-weight:bold;color:#123437">{{ __('Así de fácil es reservar:') }}</p>
    <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 6px">
        @foreach([
            __('Elige el servicio que más te guste.'),
            __('Escoge el día y la hora entre las horas libres.'),
            __('Confirma (y paga, si hace falta). Te enviaremos un correo con tu código QR.'),
            __('Al llegar, enseña el código QR a tu masajista.'),
        ] as $paso)
            <tr>
                <td style="vertical-align:top;padding:4px 10px 4px 0">
                    <span style="display:inline-block;width:24px;height:24px;line-height:24px;border-radius:12px;background:#348289;color:#ffffff;text-align:center;font-size:13px;font-weight:bold">{{ $loop->iteration }}</span>
                </td>
                <td style="padding:4px 0">{{ $paso }}</td>
            </tr>
        @endforeach
    </table>

    <p style="margin:14px 0 0">{{ __('Además, cuantas más sesiones reservas, más ahorras: con más de 10 sesiones consigues el nivel Bronze y un 10 % de descuento.') }}</p>

    @include('emails._boton', ['url' => route('servicios'), 'texto' => __('Reservar servicio')])
@endsection
