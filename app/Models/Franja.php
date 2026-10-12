<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Una hora abierta en la agenda. */
class Franja extends Model
{
    protected $fillable = ['inicio', 'bloqueada'];

    protected function casts(): array
    {
        return ['inicio' => 'datetime', 'bloqueada' => 'boolean'];
    }

    public function ocupacion()
    {
        return $this->hasOne(Ocupacion::class);
    }
}
