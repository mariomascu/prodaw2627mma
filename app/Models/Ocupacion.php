<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Qué cita (o qué solicitud de cambio) ocupa una franja. */
class Ocupacion extends Model
{
    protected $table = 'ocupaciones';

    protected $fillable = ['franja_id', 'cita_id', 'solicitud_cambio_id'];

    public function franja()
    {
        return $this->belongsTo(Franja::class);
    }

    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }
}
