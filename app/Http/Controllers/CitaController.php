<?php

namespace App\Http\Controllers;

use App\Models\Cita;

class CitaController extends Controller
{
    public function index()
    {
        $citas = Cita::with('servicio')
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->get();

        return view('citas.index', compact('citas'));
    }
}
