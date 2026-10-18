<?php

namespace Tests\Feature;

use App\Models\Servicio;
use App\Models\User;
use Tests\TestCase;

/**
 * Comprueba que todos los textos que se muestran con __() tienen traducción
 * en inglés, alemán y portugués de Brasil. Si se añade un texto nuevo a una vista
 * y no se traduce, este test falla y dice cuál falta.
 */
class TraduccionesTest extends TestCase
{
    private const IDIOMAS = ['en', 'de', 'pt_BR'];

    /** Todas las claves de traducción usadas en las vistas y en el código. */
    private function claves(): array
    {
        $ficheros = [];
        foreach (['resources/views', 'app'] as $carpeta) {
            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(base_path($carpeta)));
            foreach ($it as $f) {
                if (str_ends_with($f, '.php')) {
                    $ficheros[] = (string) $f;
                }
            }
        }

        $claves = [];
        foreach ($ficheros as $f) {
            $s = file_get_contents($f);
            preg_match_all("/(?:__|@lang|Lang::get|trans)\(\s*'((?:[^'\\\\]|\\\\.)*)'/", $s, $m);
            foreach ($m[1] as $k) {
                $claves[stripslashes($k)] = true;
            }
        }

        // Textos que se traducen a partir de variables (opciones, tipos y estados)
        foreach ([User::SEXOS, User::OBJETIVOS, User::FRECUENCIAS, Servicio::TIPOS, \App\Models\Ajuste::DIAS] as $opciones) {
            foreach ($opciones as $texto) {
                $claves[$texto] = true;
            }
        }
        foreach (['Pendiente', 'Confirmada', 'Cancelada', 'Pagado', 'Reembolsado', 'Cancelado', 'Duración del curso', 'Duración del servicio', 'Sesiones al mes'] as $texto) {
            $claves[$texto] = true;
        }

        // Las claves de grupo (validation.required, auth.failed…) van en los ficheros PHP de lang/
        return array_filter(array_keys($claves), fn ($k) => ! preg_match('/^[a-z_]+\.[a-z_.]+$/', $k));
    }

    public function test_todos_los_textos_estan_traducidos(): void
    {
        $textosIngleses = array_keys(json_decode(file_get_contents(lang_path('es.json')), true));

        foreach (self::IDIOMAS as $idioma) {
            $traducciones = json_decode(file_get_contents(lang_path("$idioma.json")), true);

            $faltan = array_filter($this->claves(), function ($clave) use ($idioma, $traducciones, $textosIngleses) {
                // En inglés, los textos de Laravel/Breeze ya están en inglés
                if ($idioma === 'en' && in_array($clave, $textosIngleses, true)) {
                    return false;
                }

                return ! isset($traducciones[$clave]);
            });

            $this->assertSame([], array_values($faltan), "Textos sin traducir en lang/$idioma.json");
        }
    }

    public function test_los_textos_de_laravel_estan_traducidos_en_aleman_y_portugues(): void
    {
        $textosIngleses = array_keys(json_decode(file_get_contents(lang_path('es.json')), true));

        foreach (['de', 'pt_BR'] as $idioma) {
            $traducciones = json_decode(file_get_contents(lang_path("$idioma.json")), true);
            $faltan = array_diff($textosIngleses, array_keys($traducciones));

            $this->assertSame([], array_values($faltan), "Textos de Laravel sin traducir en lang/$idioma.json");
        }
    }

    public function test_los_mensajes_de_validacion_estan_completos(): void
    {
        $aplanar = function (array $a, string $prefijo = '') use (&$aplanar) {
            $r = [];
            foreach ($a as $k => $v) {
                if (in_array($k, ['custom', 'attributes'], true)) {
                    continue;
                }
                $r = is_array($v) ? array_merge($r, $aplanar($v, "$prefijo$k.")) : array_merge($r, ["$prefijo$k"]);
            }

            return $r;
        };

        $referencia = $aplanar(require base_path('vendor/laravel/framework/src/Illuminate/Translation/lang/en/validation.php'));
        $atributosEs = array_keys((require lang_path('es/validation.php'))['attributes']);

        foreach (['es', 'de', 'pt_BR'] as $idioma) {
            $validacion = require lang_path("$idioma/validation.php");
            $this->assertSame([], array_values(array_diff($referencia, $aplanar($validacion))), "Reglas sin traducir en lang/$idioma/validation.php");
        }

        foreach (self::IDIOMAS as $idioma) {
            $atributos = array_keys((require lang_path("$idioma/validation.php"))['attributes']);
            $this->assertSame([], array_values(array_diff($atributosEs, $atributos)), "Campos sin traducir en lang/$idioma/validation.php");
        }
    }

    public function test_las_paginas_largas_existen_en_todos_los_idiomas(): void
    {
        foreach (['es', ...self::IDIOMAS] as $idioma) {
            foreach (['acerca_de', 'aviso_legal', 'politica_privacidad', 'terminos_condiciones'] as $pagina) {
                $this->assertFileExists(resource_path("views/info/textos/$idioma/$pagina.blade.php"));
            }
        }
    }
}
