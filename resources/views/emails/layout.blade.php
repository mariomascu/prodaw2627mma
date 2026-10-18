{{--
    Plantilla común de los correos. Los programas de correo (Gmail, Outlook…) no entienden el CSS
    moderno: el diseño se hace con tablas y los estilos van escritos en cada etiqueta.

    Cabecera: es una imagen (public/img/email/cabecera.png, logo + «YoguiFit» en la fuente Montez)
    incrustada en el correo, porque Gmail y Outlook no cargan fuentes web: así se ve igual en todos.
    El texto usa Geologica (la fuente de la web) donde el programa de correo lo permite (Apple Mail,
    iPhone, Thunderbird…); si no, Arial.
--}}
@php
    $telefono  = \App\Models\Ajuste::valor('centro_telefono');
    $email     = \App\Models\Ajuste::valor('centro_email');
    $direccion = \App\Models\Ajuste::valor('centro_direccion');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>YoguiFit</title>
    <link href="https://fonts.googleapis.com/css2?family=Geologica:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body, table, td, p, a, h1, div { font-family: 'Geologica', Arial, Helvetica, sans-serif; }
    </style>
</head>
<body style="margin:0;padding:0;background:#eef4f3;font-family:'Geologica',Arial,Helvetica,sans-serif;color:#1a1a1a">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#eef4f3;padding:24px 12px">
    <tr><td align="center">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:14px;overflow:hidden">
            {{-- Cabecera --}}
            <tr>
                <td bgcolor="#1f5a5f" style="background:#1f5a5f;background:linear-gradient(135deg,#123437,#348289);padding:14px 0 22px;text-align:center">
                    <a href="{{ url('/') }}" style="text-decoration:none">
                        <img src="{{ $message->embed(public_path('img/email/cabecera.png')) }}" width="600" alt="YoguiFit"
                             style="display:block;width:100%;max-width:600px;height:auto;border:0;color:#ffffff;font-size:32px">
                    </a>
                    <div style="font-size:13px;color:#d7e5e3;margin-top:2px;padding:0 20px">{{ __('Especialistas en terapia deportiva y masaje Thai Fusión') }}</div>
                </td>
            </tr>

            {{-- Contenido --}}
            <tr><td style="padding:28px 28px 8px;font-size:15px;line-height:1.6;color:#333333">
                @yield('contenido')
            </td></tr>

            {{-- Pie --}}
            <tr>
                <td style="padding:20px 28px 26px;border-top:1px solid #e3ecea;font-size:12px;line-height:1.6;color:#7a8a8a;text-align:center">
                    YoguiFit · {{ $direccion }}<br>
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $telefono) }}" style="color:#348289;text-decoration:none">{{ $telefono }}</a>
                    · <a href="mailto:{{ $email }}" style="color:#348289;text-decoration:none">{{ $email }}</a><br>
                    <a href="{{ url('/') }}" style="color:#348289">{{ preg_replace('#^https?://#', '', url('/')) }}</a>
                </td>
            </tr>
        </table>
    </td></tr>
</table>
</body>
</html>
