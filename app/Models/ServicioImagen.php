<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicioImagen extends Model
{
    protected $table = 'servicio_imagenes';

    protected $fillable = ['servicio_id', 'mime', 'datos'];
}
