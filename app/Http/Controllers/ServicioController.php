<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    public function index(Request $request)
    {
        $tipo = $request->get('tipo', 'cursos');
        $tipos = ['cursos', 'masajes', 'yoga'];
        if (!in_array($tipo, $tipos)) {
            $tipo = 'cursos';
        }

        $servicios = Servicio::where('tipo', $tipo)->orderBy('orden')->get();

        return view('servicios.index', compact('servicios', 'tipo'));
    }
}
