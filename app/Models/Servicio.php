<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    // El orden de esta lista es el de las pestañas de Servicios y las secciones de Tarifas
    public const TIPOS = [
        'masajes' => 'Masajes',
        'yoga'    => 'Yoga',
        'cursos'  => 'Cursos',
    ];

    // Tipos que se reservan eligiendo día y hora en la agenda (los cursos van por solicitud)
    public const TIPOS_CON_AGENDA = ['masajes', 'yoga'];

    protected $fillable = ['tipo', 'titulo', 'descripcion', 'imagen', 'duracion', 'precio', 'orden'];

    protected function casts(): array
    {
        return [
            'imagen_actualizada_at' => 'datetime',
        ];
    }

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }

    public function imagenGuardada()
    {
        return $this->hasOne(ServicioImagen::class);
    }

    public function traducciones()
    {
        return $this->hasMany(ServicioTraduccion::class);
    }

    /**
     * Devuelve un campo (titulo, descripcion, duracion o precio) en el idioma actual.
     * Si no hay traducción para ese idioma, devuelve el texto en español.
     *   {{ $servicio->t('titulo') }}
     */
    public function t(string $campo): ?string
    {
        $idioma = app()->getLocale();

        if ($idioma !== 'es') {
            $traduccion = $this->traducciones->firstWhere('idioma', $idioma);
            if ($traduccion && filled($traduccion->$campo)) {
                return $traduccion->$campo;
            }
        }

        return $this->$campo;
    }

    /**
     * URL de la imagen del servicio.
     * 1) Si hay imagen guardada en la base de datos, se sirve desde la ruta servicios.imagen.
     * 2) Si no, se busca en public/img un fichero con el nombre de la columna `imagen`.
     */
    public function getImagenUrlAttribute(): ?string
    {
        if ($this->imagen_actualizada_at) {
            return route('servicios.imagen', [
                'servicio' => $this->id,
                'v' => $this->imagen_actualizada_at->timestamp,
            ]);
        }

        if (! $this->imagen) {
            return null;
        }

        foreach (['webp', 'jpg', 'jpeg', 'png'] as $ext) {
            if (is_file(public_path("img/{$this->imagen}.{$ext}"))) {
                return asset("img/{$this->imagen}.{$ext}");
            }
        }

        return null;
    }

    public function usaAgenda(): bool
    {
        return in_array($this->tipo, self::TIPOS_CON_AGENDA, true);
    }

    /**
     * Minutos que dura una sesión, sacados del texto de "duracion" ("60 min", "1 h 30 min"…).
     * En yoga la duración indica sesiones al mes, así que cada sesión cuenta como 60 minutos.
     */
    public function minutosSesion(): int
    {
        if ($this->tipo === 'yoga') {
            return 60;
        }

        $minutos = 0;
        if (preg_match('/(\d+)\s*h/i', (string) $this->duracion, $m)) {
            $minutos += (int) $m[1] * 60;
        }
        if (preg_match('/(\d+)\s*min/i', (string) $this->duracion, $m)) {
            $minutos += (int) $m[1];
        }

        return $minutos > 0 ? $minutos : 60;
    }

    /** Horas de agenda que ocupa una sesión (un masaje de 95 min ocupa 2). */
    public function horasNecesarias(): int
    {
        return max(1, (int) ceil($this->minutosSesion() / 60));
    }

    /**
     * Aplica un descuento al primer número del precio y respeta el resto del texto:
     * "90 €" con 10 % → "81 €"; "160 €/mes" con 15 % → "136 €/mes".
     */
    public static function aplicarDescuento(string $precio, int $porcentaje): string
    {
        if ($porcentaje <= 0 || ! preg_match('/\d+(?:[.,]\d+)?/', $precio, $m)) {
            return $precio;
        }

        $valor = (float) str_replace(',', '.', $m[0]) * (100 - $porcentaje) / 100;
        $texto = fmod($valor, 1) == 0 ? number_format($valor, 0, ',', '.') : number_format($valor, 2, ',', '.');

        return preg_replace('/\d+(?:[.,]\d+)?/', $texto, $precio, 1);
    }

    /** Etiqueta del campo duración según el tipo de servicio. */
    public static function etiquetaDuracion(?string $tipo): string
    {
        return match ($tipo) {
            'cursos' => 'Duración del curso',
            'yoga'   => 'Sesiones al mes',
            default  => 'Duración del servicio',
        };
    }
}
