<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
    protected $table = 'versiones';

    protected $fillable = ['numero', 'fecha', 'cambios'];

    protected function casts(): array
    {
        return ['fecha' => 'date'];
    }

    /** La versión actual es la última registrada (la de número más alto). */
    public static function actual(): ?self
    {
        return static::all()->sort(fn ($a, $b) => version_compare($b->numero, $a->numero))->first();
    }

    /** Los cambios como lista (uno por línea). */
    public function listaCambios(): array
    {
        return array_values(array_filter(array_map('trim', explode("\n", $this->cambios))));
    }
}
