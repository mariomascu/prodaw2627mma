<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $fillable = ['tipo', 'titulo', 'descripcion', 'imagen', 'duracion', 'precio', 'orden'];

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }
}
