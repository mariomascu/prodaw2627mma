<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Pack de yoga comprado por un cliente. Cada clase validada con el QR resta una sesión. */
class Bono extends Model
{
    protected $fillable = ['user_id', 'servicio_id', 'sesiones_totales', 'sesiones_usadas', 'estado'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
    }

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }

    public function pago()
    {
        return $this->hasOne(Pago::class);
    }

    /** Sesiones que quedan por realizar (las ya hechas se restan al validar el QR). */
    public function sesionesRestantes(): int
    {
        return max(0, $this->sesiones_totales - $this->sesiones_usadas);
    }

    /** Sesiones que aún se pueden reservar: las que quedan menos las ya reservadas y no realizadas. */
    public function sesionesLibres(): int
    {
        $reservadas = $this->citas()
            ->where('estado', '!=', 'cancelada')
            ->whereNull('validada_at')
            ->count();

        return max(0, $this->sesionesRestantes() - $reservadas);
    }
}
