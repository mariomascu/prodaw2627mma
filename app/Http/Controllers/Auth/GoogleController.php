<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Correos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

/**
 * Registro e inicio de sesión con Google (Laravel Socialite).
 * 1) /auth/google/redirect  → envía al usuario a la pantalla de Google.
 * 2) /auth/google/callback  → Google lo devuelve aquí con sus datos.
 */
class GoogleController extends Controller
{
    public function redirect()
    {
        abort_unless(self::configurado(), 404);

        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request)
    {
        abort_unless(self::configurado(), 404);

        // Si el usuario cancela en Google o algo falla, se vuelve al login con un aviso
        try {
            $google = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('login')->withErrors(['email' => __('No se ha podido iniciar sesión con Google. Inténtalo de nuevo.')]);
        }

        // Solo se aceptan correos verificados por Google: así nadie puede entrar con un correo ajeno
        if (! $google->getEmail() || ! ($google->user['email_verified'] ?? false)) {
            return redirect()->route('login')->withErrors(['email' => __('Tu cuenta de Google no tiene el correo verificado.')]);
        }

        $user = $this->buscarOCrear($google->getId(), Str::lower($google->getEmail()), $google->getName());

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        if ($user->is_admin) {
            return redirect()->intended(route('admin.dashboard'));
        }

        // Igual que en el registro normal: si aún no ha respondido el cuestionario, va a responderlo.
        // Si venía de elegir un servicio, después volverá a él (la URL guardada se mantiene).
        if (! $user->perfilCompleto()) {
            return redirect()->route('cuestionario');
        }

        return redirect()->intended(route('citas'));
    }

    /**
     * 1) Ya había entrado con Google → se le encuentra por google_id.
     * 2) Ya tenía cuenta con ese correo (registro normal) → se enlaza su cuenta con Google.
     * 3) Es nuevo → se crea la cuenta con el correo ya verificado.
     */
    private function buscarOCrear(string $googleId, string $email, ?string $nombre): User
    {
        if ($user = User::where('google_id', $googleId)->first()) {
            return $user;
        }

        if ($user = User::where('email', $email)->first()) {
            $user->forceFill([
                'google_id'         => $googleId,
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();

            return $user;
        }

        $user = User::create([
            'name'      => $nombre ?: Str::before($email, '@'),
            'email'     => $email,
            'google_id' => $googleId,
            'idioma'    => app()->getLocale(),
            // No usará contraseña, pero la columna es obligatoria: se guarda una aleatoria.
            // Si algún día quiere una, puede crearla con «¿Olvidaste tu contraseña?».
            'password'  => Str::random(40),
        ]);
        $user->forceFill(['email_verified_at' => now()])->save();

        Correos::bienvenida($user);   // solo a las cuentas nuevas

        return $user;
    }

    /** El botón solo aparece si las claves de Google están configuradas. */
    public static function configurado(): bool
    {
        return filled(config('services.google.client_id')) && filled(config('services.google.client_secret'));
    }
}
