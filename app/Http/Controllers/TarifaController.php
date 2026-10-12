<?php

namespace App\Http\Controllers;

use App\Models\Servicio;

class TarifaController extends Controller
{
    public function index()
    {
        // Todos los servicios de una sola consulta, agrupados por tipo (masajes, yoga, cursos)
        $porTipo = Servicio::with('traducciones')->orderBy('orden')->get()->groupBy('tipo');

        return view('tarifas.index', compact('porTipo'));
    }
}
