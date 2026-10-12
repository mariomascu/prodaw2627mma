<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Servicio;
use App\Models\SolicitudCambio;
use App\Models\User;

/** Portada del panel: resumen y estadísticas de los clientes. */
class DashboardController extends Controller
{
    public function index()
    {
        $clientes = User::where('is_admin', false);

        // Cuántos clientes hay de cada respuesta del cuestionario
        $porSexo       = $this->contar('sexo', User::SEXOS);
        $porObjetivo   = $this->contar('objetivo', User::OBJETIVOS);
        $porFrecuencia = $this->contar('frecuencia', User::FRECUENCIAS);

        // Reservas por tipo de servicio (cursos, masajes, yoga)
        $reservasPorTipo = [];
        foreach (Servicio::TIPOS as $clave => $nombre) {
            $reservasPorTipo[$nombre] = Cita::whereHas('servicio', fn ($q) => $q->where('tipo', $clave))->count();
        }

        // Los 5 servicios más reservados
        $masReservados = Servicio::withCount('citas')
            ->orderByDesc('citas_count')
            ->take(5)
            ->get()
            ->where('citas_count', '>', 0);

        return view('admin.dashboard', [
            'totalClientes'      => (clone $clientes)->count(),
            'clientesSinPerfil'  => (clone $clientes)->whereNull('perfil_completado_at')->count(),
            'citasPendientes'    => Cita::where('estado', 'pendiente')->count(),
            'cambiosPendientes'  => SolicitudCambio::where('estado', 'pendiente')->count(),
            'totalCitas'         => Cita::count(),
            'totalServicios'     => Servicio::count(),
            'porSexo'            => $porSexo,
            'porObjetivo'        => $porObjetivo,
            'porFrecuencia'      => $porFrecuencia,
            'reservasPorTipo'    => $reservasPorTipo,
            'masReservados'      => $masReservados,
            'ultimasCitas'       => Cita::with(['user', 'servicio'])->latest()->take(8)->get(),
        ]);
    }

    /** Devuelve [etiqueta => número de clientes] para una columna del cuestionario. */
    private function contar(string $columna, array $opciones): array
    {
        $resultado = [];
        foreach ($opciones as $clave => $etiqueta) {
            $resultado[$etiqueta] = User::where('is_admin', false)->where($columna, $clave)->count();
        }

        return $resultado;
    }
}
