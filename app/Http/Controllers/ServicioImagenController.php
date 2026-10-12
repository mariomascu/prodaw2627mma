<?php

namespace App\Http\Controllers;

use App\Models\Servicio;

/** Devuelve la imagen de un servicio guardada en PostgreSQL. */
class ServicioImagenController extends Controller
{
    public function show(Servicio $servicio)
    {
        $imagen = $servicio->imagenGuardada;

        abort_unless($imagen, 404);

        return response(base64_decode($imagen->datos))
            ->header('Content-Type', $imagen->mime)
            // La URL lleva ?v=fecha, así que el navegador puede guardarla en caché mucho tiempo
            ->header('Cache-Control', 'public, max-age=31536000, immutable');
    }
}
