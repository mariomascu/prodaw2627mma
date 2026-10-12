<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    public function index(Request $request)
    {
        $tipo = $request->get('tipo', 'masajes');
        if (! array_key_exists($tipo, Servicio::TIPOS)) {
            $tipo = 'masajes';
        }

        $servicios = Servicio::with('traducciones')->where('tipo', $tipo)->orderBy('orden')->get();

        return view('servicios.index', compact('servicios', 'tipo'));
    }
}
