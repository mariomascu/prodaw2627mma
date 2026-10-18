<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'email', 'telefono', 'password', 'sexo', 'objetivo', 'frecuencia', 'perfil_completado_at', 'idioma', 'google_id'])]
#[Hidden(['password', 'remember_token', 'google_id'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // Opciones del cuestionario del registro (se usan en el formulario, la validación y las estadísticas)
    public const SEXOS = [
        'masculino' => 'Masculino',
        'femenino'  => 'Femenino',
    ];

    public const OBJETIVOS = [
        'relajacion'   => 'Relajación y reducción del estrés',
        'dolor'        => 'Alivio de dolor muscular o lesiones',
        'rendimiento'  => 'Mejora del rendimiento deportivo',
        'bienestar'    => 'Bienestar general y salud integral',
    ];

    public const FRECUENCIAS = [
        'primera_vez' => 'Primera vez, quiero probar',
        'mensual'     => 'Una vez al mes',
        'quincenal'   => 'Cada dos semanas',
        'semanal'     => 'Semanalmente',
    ];

    // Niveles de fidelidad: [nombre, más de X sesiones reservadas, % de descuento]
    public const NIVELES = [
        'gold'   => ['Gold', 30, 20],
        'silver' => ['Silver', 20, 15],
        'bronze' => ['Bronze', 10, 10],
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'perfil_completado_at' => 'datetime',
            'is_admin' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }

    public function bonos()
    {
        return $this->hasMany(Bono::class);
    }

    /** Bono activo de un servicio de yoga con sesiones aún por reservar (o null). */
    public function bonoDisponible(Servicio $servicio): ?Bono
    {
        return $this->bonos()->where('servicio_id', $servicio->id)->where('estado', 'activo')->get()
            ->first(fn (Bono $bono) => $bono->sesionesLibres() > 0);
    }

    /** Sesiones reservadas por el cliente (todas menos las canceladas). */
    public function sesionesReservadas(): int
    {
        return $this->citas()->where('estado', '!=', 'cancelada')->count();
    }

    /**
     * Nivel de fidelidad según las sesiones reservadas, o null si aún no tiene.
     * Devuelve ['clave' => 'silver', 'nombre' => 'Silver', 'descuento' => 15].
     */
    public function nivel(?int $sesiones = null): ?array
    {
        $sesiones ??= $this->sesionesReservadas();

        foreach (self::NIVELES as $clave => [$nombre, $minimo, $descuento]) {
            if ($sesiones > $minimo) {
                return compact('clave', 'nombre', 'descuento');
            }
        }

        return null;
    }

    /** Sesiones que faltan para el siguiente nivel, o null si ya es Gold. */
    public function siguienteNivel(?int $sesiones = null): ?array
    {
        $sesiones ??= $this->sesionesReservadas();

        foreach (array_reverse(self::NIVELES, true) as $clave => [$nombre, $minimo, $descuento]) {
            if ($sesiones <= $minimo) {
                return ['nombre' => $nombre, 'faltan' => $minimo + 1 - $sesiones, 'descuento' => $descuento];
            }
        }

        return null;
    }

    public function perfilCompleto(): bool
    {
        return $this->perfil_completado_at !== null;
    }
}
