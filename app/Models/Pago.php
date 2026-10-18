<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Un cobro con Stripe (cita de masaje, curso aceptado o bono de yoga). El importe va en céntimos. */
class Pago extends Model
{
    protected $fillable = [
        'user_id', 'cita_id', 'bono_id', 'concepto', 'importe', 'estado',
        'stripe_session_id', 'stripe_payment_intent', 'pagado_at', 'reembolsado_at',
    ];

    protected function casts(): array
    {
        return ['pagado_at' => 'datetime', 'reembolsado_at' => 'datetime'];
    }

    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }

    public function bono()
    {
        return $this->belongsTo(Bono::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Importe en euros con formato: 7650 → "76,50 €". */
    public function importeTexto(): string
    {
        $euros = $this->importe / 100;

        return (fmod($euros, 1) == 0 ? number_format($euros, 0, ',', '.') : number_format($euros, 2, ',', '.')) . ' €';
    }
}
