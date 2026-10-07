@extends('layouts.yoguifit')
@section('title', 'Aviso Legal — YoguiFit')
@section('content')
<div class="page-banner">
    <h1><i class="bi bi-file-earmark-text"></i> Aviso Legal</h1>
</div>
<div class="container section" style="max-width:760px">
    <div class="card-yf card-pad">
        <h2>Información general</h2>
        <p>
            Este aviso legal regula el acceso, así como la compra de los servicios e información contenida o
            comercializada en el sitio web www.yoguifit.com (el «Sitio Web»).
        </p>

        <h3>Titular</h3>
        <p>
            YoguiFit Studio de Masajes<br>
            C. Farmacéutico Moreno Chica, 6, 29749 Almayate, Vélez-Málaga (Málaga)<br>
            Teléfono / WhatsApp: <a href="tel:+34644146948" style="color:var(--primary)">+34 644 146 948</a><br>
            Email: <a href="mailto:reservas@yoguifit.com" style="color:var(--primary)">reservas@yoguifit.com</a>
        </p>

        <h3>Objeto</h3>
        <p>
            Cualquier persona que tenga la intención de realizar la compra de servicios del Sitio Web («Usuario») o,
            incluso, tener acceso a la información disponible, debe certificar que entiende y acepta las condiciones
            de uso del Sitio Web.
        </p>
        <p>
            El Usuario está consciente y acepta que la información está disponible solo con fines informativos, para
            uso facultativo del Usuario a su propia discreción y bajo su propia responsabilidad. Las condiciones de
            venta de los servicios anunciados se describen en los
            <a href="{{ route('terminos-condiciones') }}" style="color:var(--primary)">Términos y Condiciones</a>
            y en la página de cada servicio.
        </p>

        <h3>Registro de usuario y acceso al Sitio Web</h3>
        <p>
            La compra de servicios disponibles en el Sitio Web solo puede ser realizada por personas mayores de
            dieciocho (18) años que tengan la capacidad legal de contratar y una dirección de correo electrónico válida.
            El registro de Usuario es personal, único e intransferible, y el Usuario es responsable de que los datos
            proporcionados sean correctos, completos y verdaderos, así como de mantenerlos actualizados.
        </p>
        <p>
            El Sitio Web se reserva el derecho de verificar la exactitud de los datos de registro y de bloquear,
            suspender o cancelar el registro si se comprueba que el Usuario ha proporcionado datos incorrectos o falsos.
            El registro en el Sitio Web es gratuito.
        </p>

        <h3>Testimonios</h3>
        <p>
            El Sitio Web puede publicar testimonios de los Usuarios sobre los servicios. Dichos testimonios se ofrecen
            con el fin de compartir la experiencia de los Usuarios y no deben ser interpretados como una garantía.
        </p>

        <h3>Limitación de responsabilidad</h3>
        <p>
            La tolerancia al incumplimiento, por cualquiera de las partes, de las disposiciones contenidas en estas
            condiciones no deberá ser interpretada como renuncia o novación. El Sitio Web no será responsable, en
            ninguna circunstancia, de indemnizaciones por daños indirectos o lucro cesante.
        </p>

        <h3>Vigencia y modificaciones</h3>
        <p>
            Estas condiciones entran en vigor a partir de su publicación en el Sitio Web y permanecerán vigentes hasta
            que sean reemplazadas por una nueva versión, que se aplicará desde la fecha de su publicación.
        </p>

        <h3>Legislación aplicable</h3>
        <p>
            Estas condiciones se rigen por la Ley 7/1998, de 13 de abril, sobre condiciones generales de la contratación.
        </p>
    </div>
</div>
@endsection
