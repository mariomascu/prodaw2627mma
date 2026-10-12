<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicioTraduccion extends Model
{
    protected $table = 'servicio_traducciones';

    protected $fillable = ['servicio_id', 'idioma', 'titulo', 'descripcion', 'duracion', 'precio'];
}
