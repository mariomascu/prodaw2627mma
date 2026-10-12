<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Petición del cliente para mover su cita a otro día u hora. */
class SolicitudCambio extends Model
{
    protected $table = 'solicitudes_cambio';

    protected $fillable = ['cita_id', 'inicio', 'fin', 'estado'];

    protected function casts(): array
    {
        return ['inicio' => 'datetime', 'fin' => 'datetime'];
    }

    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }
}
