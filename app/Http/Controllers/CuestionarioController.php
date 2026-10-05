<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Servicio;
use Illuminate\Http\Request;

class CuestionarioController extends Controller
{
    public function index(Request $request)
    {
        $servicioId = $request->get('servicio_id');
        $servicio = $servicioId ? Servicio::find($servicioId) : null;
        return view('cuestionario.index', compact('servicio'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string|max:100',
            'telefono' => 'required|string|max:20',
            'sexo'     => 'nullable|in:masculino,femenino',
        ]);

        Cita::create([
            'user_id'      => auth()->id(),
            'servicio_id'  => $request->servicio_id ?: null,
            'nombre'       => $request->nombre,
            'telefono'     => $request->telefono,
            'sexo'         => $request->sexo,
            'respuesta_2'  => $request->respuesta_2,
            'respuesta_3'  => $request->respuesta_3,
            'estado'       => 'pendiente',
        ]);

        return redirect()->route('cuestionario.gracias');
    }

    public function gracias()
    {
        return view('cuestionario.gracias');
    }
}
