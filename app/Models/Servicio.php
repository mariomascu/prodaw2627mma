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

    /**
     * URL de la imagen del servicio. En la columna `imagen` se guarda el nombre
     * del fichero de public/img sin extensión; devuelve null si no existe.
     */
    public function getImagenUrlAttribute(): ?string
    {
        if (! $this->imagen) {
            return null;
        }

        foreach (['webp', 'jpg', 'jpeg', 'png'] as $ext) {
            if (is_file(public_path("img/{$this->imagen}.{$ext}"))) {
                return asset("img/{$this->imagen}.{$ext}");
            }
        }

        return null;
    }
}
