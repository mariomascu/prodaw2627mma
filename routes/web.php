<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\TarifaController;
use App\Http\Controllers\CuestionarioController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\ServicioImagenController;
use App\Http\Controllers\IdiomaController;
use App\Http\Controllers\Admin;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Servicios
Route::get('/servicios', [ServicioController::class, 'index'])->name('servicios');

// Tarifas
Route::get('/tarifas', [TarifaController::class, 'index'])->name('tarifas');

// Cambio de idioma (selector del header, footer y menú lateral)
Route::get('/idioma/{codigo}', [IdiomaController::class, 'cambiar'])->name('idioma');

// Imagen de un servicio guardada en la base de datos
Route::get('/servicios/{servicio}/imagen', [ServicioImagenController::class, 'show'])->name('servicios.imagen');

// Reserva: se empieza eligiendo un servicio. La antigua /reservar lleva a la lista de servicios.
Route::redirect('/reservar', '/servicios');
Route::get('/reservar/{servicio}', [ReservaController::class, 'create'])->name('reservar');
Route::post('/reservar/{servicio}', [ReservaController::class, 'store'])->name('reservar.store')->middleware(['auth', 'perfil']);
Route::get('/reservar/{servicio}/disponibilidad', [ReservaController::class, 'disponibilidad'])->name('reservar.disponibilidad')->middleware(['auth', 'perfil']);

// Cuestionario: se responde una sola vez, después de registrarse
Route::middleware('auth')->group(function () {
    Route::get('/cuestionario', [CuestionarioController::class, 'index'])->name('cuestionario');
    Route::post('/cuestionario', [CuestionarioController::class, 'store'])->name('cuestionario.store');
});

// Mis citas (requiere autenticación)
Route::middleware('auth')->group(function () {
    Route::get('/mis-citas', [CitaController::class, 'index'])->name('citas');
    Route::post('/mis-citas/{cita}/cambio', [CitaController::class, 'solicitarCambio'])->name('citas.cambio');
    Route::post('/mis-citas/{cita}/cancelar', [CitaController::class, 'cancelar'])->name('citas.cancelar');
});

// Perfil de usuario (Breeze)
Route::get('/dashboard', function () {
    return auth()->user()->is_admin
        ? redirect()->route('admin.dashboard')
        : redirect()->route('citas');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Niveles de fidelidad (Bronze, Silver y Gold)
Route::get('/niveles', [HomeController::class, 'niveles'])->name('niveles');

// Páginas informativas
Route::get('/contacto', [HomeController::class, 'contacto'])->name('contacto');
Route::get('/donde-estamos', [HomeController::class, 'dondeEstamos'])->name('donde-estamos');
Route::get('/acerca-de', [HomeController::class, 'acercaDe'])->name('acerca-de');
Route::get('/aviso-legal', [HomeController::class, 'avisoLegal'])->name('aviso-legal');
Route::get('/politica-privacidad', [HomeController::class, 'politicaPrivacidad'])->name('politica-privacidad');
Route::get('/terminos-condiciones', [HomeController::class, 'terminosCondiciones'])->name('terminos-condiciones');

// Panel de administración
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/clientes', [Admin\ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/{cliente}', [Admin\ClienteController::class, 'show'])->name('clientes.show');
    Route::patch('/citas/{cita}', [Admin\CitaController::class, 'update'])->name('citas.update');
    Route::resource('servicios', Admin\ServicioController::class)->except('show');

    // Configuración (pestañas: versiones, idiomas, centro)
    Route::get('/configuracion', [Admin\ConfiguracionController::class, 'index'])->name('configuracion');
    Route::post('/configuracion/versiones', [Admin\ConfiguracionController::class, 'guardarVersion'])->name('configuracion.versiones.store');
    Route::delete('/configuracion/versiones/{version}', [Admin\ConfiguracionController::class, 'eliminarVersion'])->name('configuracion.versiones.destroy');
    Route::put('/configuracion/idiomas', [Admin\ConfiguracionController::class, 'guardarIdiomas'])->name('configuracion.idiomas');
    Route::put('/configuracion/centro', [Admin\ConfiguracionController::class, 'guardarCentro'])->name('configuracion.centro');
    Route::put('/configuracion/horario', [Admin\ConfiguracionController::class, 'guardarHorario'])->name('configuracion.horario');

    // Lector de QR: la masajista escanea el código de la cita y la valida
    Route::get('/escaner', [Admin\ValidacionController::class, 'escaner'])->name('escaner');
    Route::get('/citas/{cita}/validar/{codigo}', [Admin\ValidacionController::class, 'mostrar'])->name('citas.validar');
    Route::post('/citas/{cita}/validar/{codigo}', [Admin\ValidacionController::class, 'validar'])->name('citas.validar.store');

    // Agenda: abrir meses, bloquear horas y aprobar cambios de cita
    Route::get('/agenda', [Admin\AgendaController::class, 'index'])->name('agenda');
    Route::post('/agenda/abrir', [Admin\AgendaController::class, 'abrir'])->name('agenda.abrir');
    Route::post('/agenda/cerrar', [Admin\AgendaController::class, 'cerrar'])->name('agenda.cerrar');
    Route::post('/agenda/franjas/{franja}/bloquear', [Admin\AgendaController::class, 'bloquear'])->name('agenda.bloquear');
    Route::post('/solicitudes/{solicitud}/aprobar', [Admin\AgendaController::class, 'aprobar'])->name('solicitudes.aprobar');
    Route::post('/solicitudes/{solicitud}/rechazar', [Admin\AgendaController::class, 'rechazar'])->name('solicitudes.rechazar');
});

require __DIR__.'/auth.php';
