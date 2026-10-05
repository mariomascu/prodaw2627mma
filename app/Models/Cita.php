<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $fillable = [
        'user_id', 'servicio_id', 'nombre', 'telefono',
        'sexo', 'respuesta_2', 'respuesta_3', 'estado', 'notas'
    ];

    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
