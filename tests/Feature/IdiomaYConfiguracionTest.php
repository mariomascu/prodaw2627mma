<?php

namespace Tests\Feature;

use App\Models\Ajuste;
use App\Models\Servicio;
use App\Models\ServicioTraduccion;
use App\Models\User;
use App\Models\Version;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Selector de idioma, menú lateral, versiones y panel de configuración. */
class IdiomaYConfiguracionTest extends TestCase
{
    use RefreshDatabase;

    public function test_por_defecto_la_web_esta_en_espanol(): void
    {
        $this->get('/')->assertOk()->assertSee('Reservar servicio')->assertSee('lang="es"', false);
    }

    public function test_se_puede_cambiar_de_idioma(): void
    {
        $this->get(route('idioma', 'en'))->assertRedirect();
        $this->get('/')->assertSee('Book a service')->assertSee('lang="en"', false);

        $this->get(route('idioma', 'de'));
        $this->get('/')->assertSee('Leistung buchen');

        $this->get(route('idioma', 'pt_BR'));
        $this->get('/')->assertSee('Reservar serviço')->assertSee('lang="pt-BR"', false);
    }

    public function test_un_idioma_desconocido_da_404(): void
    {
        $this->get(route('idioma', 'fr'))->assertNotFound();
    }

    public function test_el_idioma_se_guarda_en_el_perfil_del_usuario(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('idioma', 'de'));

        $this->assertSame('de', $user->fresh()->idioma);
    }

    public function test_los_mensajes_de_validacion_salen_en_el_idioma_elegido(): void
    {
        $this->get(route('idioma', 'de'));
        $this->post('/login', ['email' => 'nadie@example.com', 'password' => 'mal'])
            ->assertSessionHasErrors(['email' => 'Diese Zugangsdaten stimmen nicht mit unseren Daten überein.']);
    }

    public function test_el_contenido_de_los_servicios_se_traduce(): void
    {
        $servicio = Servicio::create(['tipo' => 'yoga', 'titulo' => 'Pack On', 'descripcion' => 'Descripción', 'duracion' => '8 sesiones/mes', 'precio' => '160 €/mes', 'orden' => 1]);
        ServicioTraduccion::create(['servicio_id' => $servicio->id, 'idioma' => 'en', 'titulo' => 'Pack On', 'descripcion' => 'English description', 'duracion' => '8 sessions/month']);

        $this->get(route('idioma', 'en'));
        $this->get(route('servicios', ['tipo' => 'yoga']))
            ->assertSee('English description')
            ->assertSee('8 sessions/month')
            ->assertSee('160 €/mes'); // sin traducción del precio se muestra el español
    }

    public function test_el_menu_lateral_muestra_el_usuario_la_version_y_cerrar_sesion(): void
    {
        Version::create(['numero' => '0.0.11', 'fecha' => '2026-10-12', 'cambios' => 'Cambio']);
        $user = User::factory()->create(['name' => 'Ana López']);

        $this->actingAs($user)->get('/')
            ->assertSee('Ana López')
            ->assertSee('Versión 0.0.11')
            ->assertSee('Cerrar sesión');
    }

    public function test_solo_el_admin_entra_en_configuracion(): void
    {
        $this->actingAs(User::factory()->create())->get(route('admin.configuracion'))->assertForbidden();

        $admin = User::factory()->create(['is_admin' => true]);
        foreach (['versiones', 'idiomas', 'centro'] as $pestana) {
            $this->actingAs($admin)->get(route('admin.configuracion', ['pestana' => $pestana]))->assertOk();
        }
    }

    public function test_el_admin_registra_una_version_y_pasa_a_ser_la_actual(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Version::create(['numero' => '0.0.9', 'fecha' => '2026-10-12', 'cambios' => 'Antes']);

        $this->actingAs($admin)->post(route('admin.configuracion.versiones.store'), [
            'numero' => '0.0.10', 'fecha' => '2026-10-12', 'cambios' => "Uno\nDos",
        ])->assertRedirect();

        // 0.0.10 es mayor que 0.0.9 aunque como texto sea "menor"
        $this->assertSame('0.0.10', Version::actual()->numero);
        $this->assertSame(['Uno', 'Dos'], Version::actual()->listaCambios());

        $this->actingAs($admin)->post(route('admin.configuracion.versiones.store'), [
            'numero' => 'uno', 'fecha' => '2026-10-12', 'cambios' => 'x',
        ])->assertSessionHasErrors('numero');
    }

    public function test_el_admin_cambia_los_idiomas_y_los_datos_del_centro(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->put(route('admin.configuracion.idiomas'), [
            'idiomas_activos' => ['es', 'en'], 'idioma_por_defecto' => 'en',
        ])->assertRedirect();
        $this->assertSame(['es' => 'Español', 'en' => 'English'], Ajuste::idiomasActivos());

        $this->actingAs($admin)->put(route('admin.configuracion.centro'), [
            'centro_nombre' => 'YoguiFit', 'centro_telefono' => '+34 600 111 222',
            'centro_email' => 'hola@example.com', 'centro_direccion' => 'Calle Nueva 1', 'centro_instagram' => 'yoguifit',
        ])->assertRedirect();

        $this->get(route('contacto'))->assertSee('+34 600 111 222')->assertSee('hola@example.com');
    }

    public function test_el_admin_guarda_traducciones_de_un_servicio(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->post(route('admin.servicios.store'), [
            'tipo' => 'masajes', 'titulo' => 'Masaje nuevo', 'descripcion' => 'Texto', 'duracion' => '60 min', 'precio' => '50 €',
            'traducciones' => [
                'en' => ['titulo' => 'New massage', 'descripcion' => 'Text'],
                'de' => ['titulo' => '', 'descripcion' => ''],
            ],
        ])->assertRedirect();

        $servicio = Servicio::where('titulo', 'Masaje nuevo')->first();
        $this->assertDatabaseHas('servicio_traducciones', ['servicio_id' => $servicio->id, 'idioma' => 'en', 'titulo' => 'New massage']);
        $this->assertDatabaseMissing('servicio_traducciones', ['servicio_id' => $servicio->id, 'idioma' => 'de']);
    }
}
