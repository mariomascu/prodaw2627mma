<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use Illuminate\Http\Request;

class IdiomaController extends Controller
{
    /** Cambia el idioma desde el selector del header, del footer o del menú lateral. */
    public function cambiar(Request $request, string $codigo)
    {
        abort_unless(array_key_exists($codigo, Ajuste::idiomasActivos()), 404);

        $request->session()->put('idioma', $codigo);
        $request->user()?->update(['idioma' => $codigo]);

        return back();
    }
}
