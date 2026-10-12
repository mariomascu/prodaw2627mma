<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Ajustes del panel de configuración (clave => valor).
 * Uso: Ajuste::valor('telefono')  /  Ajuste::guardar('telefono', '644 146 948')
 */
class Ajuste extends Model
{
    protected $fillable = ['clave', 'valor'];

    // Valores por defecto si todavía no se han guardado desde el panel
    public const POR_DEFECTO = [
        'idioma_por_defecto' => 'es',
        'idiomas_activos'    => 'es,en,de,pt_BR',
        'centro_nombre'      => 'YoguiFit Studio de Masajes',
        'centro_telefono'    => '+34 644 146 948',
        'centro_email'       => 'reservas@yoguifit.com',
        'centro_direccion'   => 'C. Farmacéutico Moreno Chica, 6, 29749 Almayate, Vélez-Málaga (Málaga)',
        'centro_instagram'   => 'yoguifit',
        // Horario por día de la semana (1 = lunes … 7 = domingo): tramos [apertura, cierre]
        'horario' => '{"1":[["10:00","14:00"],["16:00","21:00"]],"2":[["10:00","14:00"],["16:00","21:00"]],"3":[["10:00","14:00"],["16:00","21:00"]],"4":[["10:00","14:00"],["16:00","21:00"]],"5":[["10:00","14:00"],["16:00","21:00"]],"6":[["09:00","14:00"]],"7":[]}',
    ];

    public const DIAS = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];

    // Idiomas que admite la aplicación
    public const IDIOMAS = [
        'es'    => 'Español',
        'en'    => 'English',
        'de'    => 'Deutsch',
        'pt_BR' => 'Português (Brasil)',
    ];

    /** Se cargan todos una sola vez por petición. */
    private static ?array $cache = null;

    public static function valor(string $clave): ?string
    {
        if (static::$cache === null) {
            static::$cache = static::pluck('valor', 'clave')->all();
        }

        return static::$cache[$clave] ?? static::POR_DEFECTO[$clave] ?? null;
    }

    /** Vacía la caché para que se vuelvan a leer de la base de datos. */
    public static function olvidar(): void
    {
        static::$cache = null;
    }

    public static function guardar(string $clave, ?string $valor): void
    {
        static::updateOrCreate(['clave' => $clave], ['valor' => $valor]);
        static::$cache = null;
    }

    /** Horario del centro: [día (1-7) => [[apertura, cierre], …]]. Un día sin tramos está cerrado. */
    public static function horario(): array
    {
        $horario = json_decode(static::valor('horario'), true) ?: [];

        $resultado = [];
        foreach (array_keys(self::DIAS) as $dia) {
            $resultado[$dia] = $horario[$dia] ?? [];
        }

        return $resultado;
    }

    /** URL de la bandera de un idioma (public/img/banderas/<codigo>.svg). */
    public static function bandera(string $codigo): string
    {
        return asset("img/banderas/{$codigo}.svg");
    }

    /** Idiomas que aparecen en el selector: [codigo => nombre]. */
    public static function idiomasActivos(): array
    {
        $codigos = explode(',', static::valor('idiomas_activos'));

        return array_intersect_key(static::IDIOMAS, array_flip($codigos));
    }
}
