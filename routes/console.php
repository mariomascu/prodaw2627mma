<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Copia a la base de datos las imágenes de public/img de los servicios que aún no la tienen guardada.
Artisan::command('servicios:importar-imagenes', function () {
    $tipos = ['webp' => 'image/webp', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];

    foreach (\App\Models\Servicio::whereNull('imagen_actualizada_at')->whereNotNull('imagen')->get() as $servicio) {
        foreach ($tipos as $ext => $mime) {
            $ruta = public_path("img/{$servicio->imagen}.{$ext}");
            if (is_file($ruta)) {
                $servicio->imagenGuardada()->updateOrCreate([], [
                    'mime'  => $mime,
                    'datos' => base64_encode(file_get_contents($ruta)),
                ]);
                $servicio->imagen_actualizada_at = now();
                $servicio->save();
                $this->info("✔ {$servicio->titulo}");
                break;
            }
        }
    }
})->purpose('Guarda en PostgreSQL las imágenes de public/img de los servicios');
