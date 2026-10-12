<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Cita extends Model
{
    protected $fillable = [
        'user_id', 'servicio_id', 'nombre', 'telefono',
        'sexo', 'respuesta_2', 'respuesta_3', 'estado', 'notas',
        'inicio', 'fin', 'descuento', 'precio_final', 'validada_at',
    ];

    protected function casts(): array
    {
        return ['inicio' => 'datetime', 'fin' => 'datetime', 'validada_at' => 'datetime'];
    }

    /** Cada cita nueva recibe un código secreto para su QR. */
    protected static function booted(): void
    {
        static::creating(function (Cita $cita) {
            $cita->codigo ??= Str::random(32);
        });
    }

    /** Enlace que va dentro del QR; al abrirlo, el administrador ve la cita y puede validarla. */
    public function urlValidacion(): string
    {
        return route('admin.citas.validar', ['cita' => $this->id, 'codigo' => $this->codigo]);
    }

    /** La cita tiene QR si está confirmada, tiene día y hora y aún no se ha realizado. */
    public function tieneQr(): bool
    {
        return $this->estado === 'confirmada' && $this->inicio !== null && $this->validada_at === null;
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ocupaciones()
    {
        return $this->hasMany(Ocupacion::class);
    }

    public function solicitudes()
    {
        return $this->hasMany(SolicitudCambio::class);
    }

    /** Solicitud de cambio pendiente de aprobar (si la hay). */
    public function solicitudPendiente()
    {
        return $this->hasOne(SolicitudCambio::class)->where('estado', 'pendiente');
    }

    /** El cliente solo puede cancelar con 24 horas de antelación. */
    public function sePuedeCancelar(): bool
    {
        return $this->estado !== 'cancelada'
            && $this->validada_at === null
            && $this->inicio !== null
            && $this->inicio->greaterThan(now()->addHours(24));
    }

    /** El cliente puede pedir un cambio si la cita es futura, está activa y no tiene otro cambio pendiente. */
    public function sePuedeCambiar(): bool
    {
        return $this->estado === 'confirmada'
            && $this->validada_at === null
            && $this->inicio !== null
            && $this->inicio->isFuture()
            && ! $this->solicitudPendiente;
    }
}
