<?php

namespace App\Database;

use Illuminate\Database\PostgresConnection;

/**
 * Conexión PostgreSQL adaptada a Neon.
 *
 * Con PDO::ATTR_EMULATE_PREPARES (necesario para el pooler de Neon) los valores se
 * escriben directamente en el SQL. Laravel convierte true/false en 1/0 y PostgreSQL
 * no acepta un entero en una columna boolean, así que aquí se envían como 'true'/'false'.
 */
class NeonPostgresConnection extends PostgresConnection
{
    public function prepareBindings(array $bindings)
    {
        foreach ($bindings as $key => $value) {
            if (is_bool($value)) {
                $bindings[$key] = $value ? 'true' : 'false';
            }
        }

        return parent::prepareBindings($bindings);
    }
}
