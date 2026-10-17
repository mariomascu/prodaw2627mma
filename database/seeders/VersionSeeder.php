<?php

namespace Database\Seeders;

use App\Models\Version;
use Illuminate\Database\Seeder;

/**
 * Historial de versiones desde el nacimiento del proyecto.
 * Se puede ejecutar varias veces: solo añade las versiones que falten.
 *   php artisan db:seed --class=VersionSeeder
 */
class VersionSeeder extends Seeder
{
    public function run(): void
    {
        $versiones = [
            ['0.0.1', '2026-10-05', [
                'Proyecto inicial: Laravel 13 con Breeze.',
                'Portada, servicios, tarifas, reserva mediante cuestionario y «Mis citas».',
                'Páginas informativas: contacto, dónde estamos, acerca de y textos legales.',
            ]],
            ['0.0.2', '2026-10-05', [
                'Despliegue en Vercel.',
                'Base de datos PostgreSQL en Neon.',
            ]],
            ['0.0.3', '2026-10-05', [
                'La raíz del sitio ejecuta Laravel en Vercel.',
                'La función de Vercel se ejecuta en Londres, junto a la base de datos.',
            ]],
            ['0.0.4', '2026-10-05', [
                'Los errores de producción se registran en una sola línea en los logs de Vercel.',
            ]],
            ['0.0.5', '2026-10-05', [
                'Conexión a Neon desde Vercel sin soporte SNI.',
            ]],
            ['0.0.6', '2026-10-07', [
                'Portada: tarjetas de acceso en una fila en escritorio y en dos filas en móvil.',
                'Márgenes corregidos en la versión móvil.',
                'Dirección real en «Dónde estamos» y textos de yoguifit.com en «Acerca de», «Aviso legal» y «Política de privacidad».',
            ]],
            ['0.0.7', '2026-10-07', [
                'Imágenes de los servicios añadidas a public/img.',
            ]],
            ['0.0.8', '2026-10-08', [
                'Las tarjetas de servicios muestran su imagen con animación de carga.',
            ]],
            ['0.0.9', '2026-10-12', [
                'La reserva se hace desde cada servicio; se elimina la pestaña «Reservar».',
                'Botón «Reservar servicio» en la portada.',
                'Cuestionario de perfil una sola vez, al registrarse.',
                'Panel de administración: estadísticas, clientes y gestión de servicios.',
                'Imágenes de los servicios guardadas en PostgreSQL.',
                'Inicio de sesión con correo o nombre de usuario.',
            ]],
            ['0.0.10', '2026-10-12', [
                'Todos los mensajes de error, registro y validación en español.',
            ]],
            ['0.0.11', '2026-10-12', [
                'Panel de configuración para el administrador: versiones, idiomas y datos del centro.',
                'Menú lateral con el nombre del usuario, la versión de la aplicación y «Cerrar sesión».',
                'Los datos de contacto del centro se gestionan desde el panel.',
            ]],
            ['0.0.12', '2026-10-12', [
                'Aplicación traducida al inglés, alemán y portugués de Brasil, además del español.',
                'Selector de idioma con banderas en el header, el menú lateral y el pie de página.',
                'Traducción del contenido de los servicios desde el panel de administración.',
                'Textos legales y «Acerca de» en los cuatro idiomas.',
            ]],
            ['0.0.13', '2026-10-12', [
                'Portada: fondo de mandala en el hero, botones en verde turquesa y nombre «YoguiFit» en turquesa.',
                'Logotipo de YoguiFit monocromo delante del nombre en la barra de menú, en la portada y en el acceso.',
            ]],
            ['0.0.14', '2026-10-12', [
                'Botón con un ojo para mostrar u ocultar la contraseña en todos los formularios.',
            ]],
            ['0.0.15', '2026-10-12', [
                'Agenda: el administrador abre los meses y se crean las horas según el horario del centro.',
                'Horario del centro configurable (lunes a viernes 10:00–14:00 y 16:00–21:00; sábados 09:00–14:00).',
                'Masajes y yoga se reservan eligiendo día y hora en un calendario; la reserva queda confirmada al momento.',
                'Las horas reservadas dejan de estar disponibles; los masajes largos ocupan las horas que necesitan.',
                'El cliente puede pedir un cambio de cita (lo aprueba el administrador) y cancelar con 24 horas de antelación.',
                'El administrador puede bloquear horas, cancelar citas y aprobar o rechazar cambios.',
            ]],
            ['0.0.16', '2026-10-12', [
                'Niveles de fidelidad: Bronze (más de 10 sesiones, 10 %), Silver (más de 20, 15 %) y Gold (más de 30, 20 %).',
                'Panel de clientes: columna de nombre más ancha, nivel y número de sesiones.',
                'Servicios y tarifas ordenados: masajes, yoga y cursos.',
                'El menú lateral muestra solo la bandera de cada idioma.',
            ]],
            ['0.0.17', '2026-10-13', [
                'Mensaje de confirmación antes de reservar: la cita solo pasa a «Mis citas» al confirmarla.',
                'Código QR en cada cita confirmada con los datos de la cita.',
                'Lector de QR para el administrador (icono junto al menú): abre la cámara, muestra la cita y la clienta y valida la sesión como realizada.',
                'Letrero salmón «Consigue un X % de descuento con tu nivel…» y página que explica los niveles Bronze, Silver y Gold.',
                'Selector de idioma: solo banderas en el pie, el desplegable del header se cierra al pulsar fuera y en el menú lateral solo aparece en móvil.',
            ]],
            ['0.0.18', '2026-10-17', [
                'Registro e inicio de sesión con Google («Continuar con Google»).',
                'Si ya existía una cuenta con el mismo correo, se enlaza con Google.',
                'El menú lateral ya no muestra el selector de idioma (se elige en el header y en el pie).',
            ]],
        ];

        foreach ($versiones as [$numero, $fecha, $cambios]) {
            Version::firstOrCreate(['numero' => $numero], [
                'fecha' => $fecha,
                'cambios' => implode("\n", $cambios),
            ]);
        }
    }
}
