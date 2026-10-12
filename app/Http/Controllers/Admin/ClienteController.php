<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');

        $clientes = User::where('is_admin', false)
            ->when($buscar, function ($q) use ($buscar) {
                $q->where(function ($q) use ($buscar) {
                    $q->where('name', 'ilike', "%{$buscar}%")
                      ->orWhere('email', 'ilike', "%{$buscar}%")
                      ->orWhere('telefono', 'ilike', "%{$buscar}%");
                });
            })
            ->withCount(['citas as sesiones_count' => fn ($q) => $q->where('estado', '!=', 'cancelada')])
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.clientes.index', compact('clientes', 'buscar'));
    }

    public function show(User $cliente)
    {
        abort_if($cliente->is_admin, 404);

        $citas = $cliente->citas()->with('servicio', 'solicitudPendiente')
            ->orderByRaw('inicio is null')
            ->orderByDesc('inicio')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.clientes.show', compact('cliente', 'citas'));
    }
}
