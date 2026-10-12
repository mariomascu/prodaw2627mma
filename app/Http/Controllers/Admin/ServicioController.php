<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ajuste;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Alta, edición y baja de servicios desde el panel. */
class ServicioController extends Controller
{
    public function index(Request $request)
    {
        $tipo = $request->get('tipo');

        $servicios = Servicio::withCount('citas')
            ->when(array_key_exists((string) $tipo, Servicio::TIPOS), fn ($q) => $q->where('tipo', $tipo))
            ->orderBy('tipo')
            ->orderBy('orden')
            ->get();

        return view('admin.servicios.index', compact('servicios', 'tipo'));
    }

    public function create()
    {
        return view('admin.servicios.form', ['servicio' => new Servicio(['tipo' => 'masajes'])]);
    }

    public function store(Request $request)
    {
        $servicio = Servicio::create($this->validar($request));
        $this->guardarImagen($request, $servicio);
        $this->guardarTraducciones($request, $servicio);

        return redirect()->route('admin.servicios.index')->with('success', __('Servicio creado.'));
    }

    public function edit(Servicio $servicio)
    {
        return view('admin.servicios.form', compact('servicio'));
    }

    public function update(Request $request, Servicio $servicio)
    {
        $servicio->update($this->validar($request));
        $this->guardarImagen($request, $servicio);
        $this->guardarTraducciones($request, $servicio);

        return redirect()->route('admin.servicios.index')->with('success', __('Servicio actualizado.'));
    }

    public function destroy(Servicio $servicio)
    {
        // Las citas de este servicio se conservan (su servicio_id pasa a null)
        $servicio->delete();

        return redirect()->route('admin.servicios.index')->with('success', __('Servicio eliminado.'));
    }

    private function validar(Request $request): array
    {
        $datos = $request->validate([
            'tipo'        => ['required', Rule::in(array_keys(Servicio::TIPOS))],
            'titulo'      => ['required', 'string', 'max:150'],
            'descripcion' => ['required', 'string', 'max:2000'],
            'precio'      => ['required', 'string', 'max:50'],
            'duracion'    => ['required', 'string', 'max:50'],
            'orden'       => ['nullable', 'integer', 'min:0'],
            'imagen_archivo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            // Traducciones (opcionales): traducciones[en][titulo], traducciones[de][descripcion]…
            'traducciones'                 => ['nullable', 'array'],
            'traducciones.*.titulo'        => ['nullable', 'string', 'max:150'],
            'traducciones.*.descripcion'   => ['nullable', 'string', 'max:2000'],
            'traducciones.*.duracion'      => ['nullable', 'string', 'max:50'],
            'traducciones.*.precio'        => ['nullable', 'string', 'max:50'],
        ], [
            'imagen_archivo.max' => __('La imagen no puede superar los 2 MB.'),
        ]);

        $datos['orden'] = (int) ($datos['orden'] ?? 0);
        unset($datos['imagen_archivo'], $datos['traducciones']); // se guardan aparte

        return $datos;
    }

    /**
     * Guarda las traducciones del servicio (tabla servicio_traducciones).
     * Si todos los campos de un idioma están vacíos, se borra esa traducción y se muestra el español.
     */
    private function guardarTraducciones(Request $request, Servicio $servicio): void
    {
        foreach (array_keys(Ajuste::IDIOMAS) as $idioma) {
            if ($idioma === 'es') {
                continue;
            }

            $campos = array_map(
                fn ($valor) => filled($valor) ? trim($valor) : null,
                array_intersect_key($request->input("traducciones.$idioma", []), array_flip(['titulo', 'descripcion', 'duracion', 'precio']))
            );

            if (array_filter($campos)) {
                $servicio->traducciones()->updateOrCreate(['idioma' => $idioma], $campos);
            } else {
                $servicio->traducciones()->where('idioma', $idioma)->delete();
            }
        }
    }

    /** Guarda la imagen subida en PostgreSQL (tabla servicio_imagenes) o la quita si se ha pedido. */
    private function guardarImagen(Request $request, Servicio $servicio): void
    {
        if ($request->hasFile('imagen_archivo')) {
            $archivo = $request->file('imagen_archivo');

            $servicio->imagenGuardada()->updateOrCreate([], [
                'mime'  => $archivo->getMimeType(),
                'datos' => base64_encode(file_get_contents($archivo->getRealPath())),
            ]);

            $servicio->imagen_actualizada_at = now();
            $servicio->save();
        } elseif ($request->boolean('quitar_imagen')) {
            $servicio->imagenGuardada()->delete();
            $servicio->imagen_actualizada_at = null;
            $servicio->imagen = null;
            $servicio->save();
        }
    }
}
