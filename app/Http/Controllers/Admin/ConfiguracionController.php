<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ajuste;
use App\Models\Version;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Panel de configuración con pestañas: Versiones, Idiomas y Datos del centro. */
class ConfiguracionController extends Controller
{
    public const PESTANAS = ['versiones', 'idiomas', 'centro', 'horario'];

    public function index(Request $request)
    {
        $pestana = in_array($request->get('pestana'), self::PESTANAS) ? $request->get('pestana') : 'versiones';

        $versiones = Version::all()->sort(fn ($a, $b) => version_compare($b->numero, $a->numero));

        return view('admin.configuracion', compact('pestana', 'versiones'));
    }

    public function guardarVersion(Request $request)
    {
        $datos = $request->validate([
            'numero'  => ['required', 'string', 'max:20', 'regex:/^\d+\.\d+\.\d+$/', 'unique:versiones,numero'],
            'fecha'   => ['required', 'date'],
            'cambios' => ['required', 'string', 'max:5000'],
        ], [
            'numero.regex' => __('El número de versión debe tener el formato 0.0.0.'),
        ]);

        Version::create($datos);

        return redirect()->route('admin.configuracion', ['pestana' => 'versiones'])
            ->with('success', __('Versión :numero registrada.', ['numero' => $datos['numero']]));
    }

    public function eliminarVersion(Version $version)
    {
        $version->delete();

        return redirect()->route('admin.configuracion', ['pestana' => 'versiones'])
            ->with('success', __('Versión eliminada.'));
    }

    public function guardarIdiomas(Request $request)
    {
        $codigos = array_keys(Ajuste::IDIOMAS);

        $datos = $request->validate([
            'idiomas_activos'    => ['required', 'array', 'min:1'],
            'idiomas_activos.*'  => [Rule::in($codigos)],
            'idioma_por_defecto' => ['required', Rule::in($codigos)],
        ]);

        // El idioma por defecto siempre tiene que estar activo
        $activos = array_unique([...$datos['idiomas_activos'], $datos['idioma_por_defecto']]);

        Ajuste::guardar('idiomas_activos', implode(',', $activos));
        Ajuste::guardar('idioma_por_defecto', $datos['idioma_por_defecto']);

        return redirect()->route('admin.configuracion', ['pestana' => 'idiomas'])
            ->with('success', __('Idiomas guardados.'));
    }

    /**
     * Horario del centro. Por cada día llegan dos tramos (mañana y tarde) con apertura y cierre;
     * un tramo vacío significa cerrado.
     */
    public function guardarHorario(Request $request)
    {
        $request->validate([
            'horario'         => ['required', 'array'],
            'horario.*.*.*'   => ['nullable', 'date_format:H:i'],
        ]);

        $horario = [];
        foreach (array_keys(Ajuste::DIAS) as $dia) {
            $horario[$dia] = [];
            foreach ($request->input("horario.$dia", []) as $tramo) {
                $apertura = $tramo['apertura'] ?? null;
                $cierre = $tramo['cierre'] ?? null;

                if (! $apertura && ! $cierre) {
                    continue; // tramo cerrado
                }
                if (! $apertura || ! $cierre || $apertura >= $cierre) {
                    return back()->withInput()->withErrors([
                        'horario' => __('Revisa el horario del :dia: la apertura tiene que ser anterior al cierre.', ['dia' => __(Ajuste::DIAS[$dia])]),
                    ]);
                }
                $horario[$dia][] = [$apertura, $cierre];
            }
        }

        Ajuste::guardar('horario', json_encode($horario));

        return redirect()->route('admin.configuracion', ['pestana' => 'horario'])
            ->with('success', __('Horario guardado. Se aplicará a los meses que abras a partir de ahora.'));
    }

    public function guardarCentro(Request $request)
    {
        $datos = $request->validate([
            'centro_nombre'    => ['required', 'string', 'max:150'],
            'centro_telefono'  => ['required', 'string', 'max:30'],
            'centro_email'     => ['required', 'email', 'max:150'],
            'centro_direccion' => ['required', 'string', 'max:300'],
            'centro_instagram' => ['nullable', 'string', 'max:60'],
        ]);

        foreach ($datos as $clave => $valor) {
            Ajuste::guardar($clave, $valor);
        }

        return redirect()->route('admin.configuracion', ['pestana' => 'centro'])
            ->with('success', __('Datos del centro guardados.'));
    }
}
