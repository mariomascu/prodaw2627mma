<?php

namespace Tests\Feature;

use App\Models\Servicio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as UsuarioGoogle;
use Tests\TestCase;

/** Registro e inicio de sesión con Google. La respuesta de Google se simula: no hay conexión real. */
class GoogleLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.google.client_id' => 'id-de-prueba', 'services.google.client_secret' => 'secreto-de-prueba']);
    }

    /** Simula que Google devuelve este usuario. */
    private function googleDevuelve(string $email, bool $verificado = true, string $id = 'g-123', string $nombre = 'Ana García'): void
    {
        $usuario = (new UsuarioGoogle())
            ->setRaw(['email_verified' => $verificado])
            ->map(['id' => $id, 'name' => $nombre, 'email' => $email]);

        Socialite::shouldReceive('driver->user')->andReturn($usuario);
    }

    public function test_el_boton_aparece_en_login_y_registro(): void
    {
        $this->get('/login')->assertSee('Continuar con Google')->assertSee(route('google.redirect'));
        $this->get('/register')->assertSee('Continuar con Google');
    }

    public function test_sin_claves_no_hay_boton_ni_rutas(): void
    {
        config(['services.google.client_id' => null]);

        $this->get('/login')->assertDontSee('Continuar con Google');
        $this->get(route('google.redirect'))->assertNotFound();
    }

    public function test_el_boton_lleva_a_google(): void
    {
        $this->get(route('google.redirect'))->assertRedirectContains('accounts.google.com');
    }

    public function test_un_usuario_nuevo_se_registra_y_pasa_al_cuestionario(): void
    {
        $this->googleDevuelve('ana@gmail.com');

        $this->get(route('google.callback'))->assertRedirect(route('cuestionario'));

        $ana = User::where('email', 'ana@gmail.com')->first();
        $this->assertSame('Ana García', $ana->name);
        $this->assertSame('g-123', $ana->google_id);
        $this->assertNotNull($ana->email_verified_at);
        $this->assertAuthenticatedAs($ana);
    }

    public function test_si_ya_tenia_cuenta_con_ese_correo_se_enlaza(): void
    {
        $existente = User::factory()->create(['email' => 'ana@gmail.com', 'perfil_completado_at' => now()]);
        $this->googleDevuelve('ANA@gmail.com');

        $this->get(route('google.callback'))->assertRedirect(route('citas'));

        $this->assertSame(1, User::count());
        $this->assertSame('g-123', $existente->fresh()->google_id);
        $this->assertAuthenticatedAs($existente);
    }

    public function test_la_segunda_vez_entra_con_su_cuenta_de_google(): void
    {
        $user = User::factory()->create(['email' => 'otro@correo.com', 'google_id' => 'g-123', 'perfil_completado_at' => now()]);
        // Aunque haya cambiado el correo en Google, se le reconoce por su identificador
        $this->googleDevuelve('nuevo@gmail.com');

        $this->get(route('google.callback'))->assertRedirect(route('citas'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_un_correo_sin_verificar_no_puede_entrar(): void
    {
        User::factory()->create(['email' => 'victima@gmail.com']);
        $this->googleDevuelve('victima@gmail.com', verificado: false);

        $this->get(route('google.callback'))->assertRedirect(route('login'))->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_si_google_falla_vuelve_al_login_con_un_aviso(): void
    {
        Socialite::shouldReceive('driver->user')->andThrow(new \Exception('Cancelado'));

        $this->get(route('google.callback'))->assertRedirect(route('login'))->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_si_venia_de_un_servicio_vuelve_a_el_tras_el_cuestionario(): void
    {
        $servicio = Servicio::create(['tipo' => 'masajes', 'titulo' => 'Thai', 'descripcion' => 'x', 'duracion' => '60 min', 'precio' => '90 €', 'orden' => 1]);
        $this->get(route('reservar', $servicio))->assertSee('Continuar con Google');

        $this->googleDevuelve('ana@gmail.com');
        $this->get(route('google.callback'))->assertRedirect(route('cuestionario'));

        $this->post(route('cuestionario.store'), [
            'sexo' => 'femenino', 'objetivo' => 'relajacion', 'frecuencia' => 'semanal', 'telefono' => '600000000',
        ])->assertRedirect(route('reservar', $servicio));
    }
}
