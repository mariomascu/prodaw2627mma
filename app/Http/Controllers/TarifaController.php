<?php

namespace App\Http\Controllers;

use App\Models\Servicio;

class TarifaController extends Controller
{
    public function index()
    {
        $cursos   = Servicio::where('tipo', 'cursos')->orderBy('orden')->get();
        $masajes  = Servicio::where('tipo', 'masajes')->orderBy('orden')->get();
        $yoga     = Servicio::where('tipo', 'yoga')->orderBy('orden')->get();

        return view('tarifas.index', compact('cursos', 'masajes', 'yoga'));
    }
}
