<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Services\Agenda;
use Illuminate\Http\Request;

class CitaController extends Controller
{
    /** Cambia el estado de una cita (pendiente, confirmada o cancelada). */
    public function update(Request $request, Cita $cita, Agenda $agenda)
    {
        $datos = $request->validate([
            'estado' => 'required|in:pendiente,confirmada,cancelada',
        ]);

        if ($cita->estado === 'cancelada') {
            return back()->with('error', __('Esta cita ya está cancelada.'));
        }

        // Al cancelar se liberan sus horas en la agenda
        if ($datos['estado'] === 'cancelada') {
            $agenda->cancelar($cita);
        } else {
            $cita->update($datos);
        }

        return back()->with('success', __('Estado de la cita actualizado.'));
    }
}
