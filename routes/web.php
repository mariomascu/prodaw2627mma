<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\TarifaController;
use App\Http\Controllers\CuestionarioController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\ProfileController;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Servicios
Route::get('/servicios', [ServicioController::class, 'index'])->name('servicios');

// Tarifas
Route::get('/tarifas', [TarifaController::class, 'index'])->name('tarifas');

// Cuestionario / Reserva
Route::get('/reservar', [CuestionarioController::class, 'index'])->name('cuestionario');
Route::post('/reservar', [CuestionarioController::class, 'store'])->name('cuestionario.store');
Route::get('/reservar/gracias', [CuestionarioController::class, 'gracias'])->name('cuestionario.gracias');

// Mis citas (requiere autenticación)
Route::get('/mis-citas', [CitaController::class, 'index'])->name('citas')->middleware('auth');

// Perfil de usuario (Breeze)
Route::get('/dashboard', function () {
    return redirect()->route('citas');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Páginas informativas
Route::get('/contacto', [HomeController::class, 'contacto'])->name('contacto');
Route::get('/donde-estamos', [HomeController::class, 'dondeEstamos'])->name('donde-estamos');
Route::get('/acerca-de', [HomeController::class, 'acercaDe'])->name('acerca-de');
Route::get('/aviso-legal', [HomeController::class, 'avisoLegal'])->name('aviso-legal');
Route::get('/politica-privacidad', [HomeController::class, 'politicaPrivacidad'])->name('politica-privacidad');
Route::get('/terminos-condiciones', [HomeController::class, 'terminosCondiciones'])->name('terminos-condiciones');

require __DIR__.'/auth.php';
