<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/** Flujo de reserva, cuestionario del registro y panel de administración. */
class ReservaTest extends TestCase
{
    use RefreshDatabase;

    private function servicio(string $tipo = 'masajes'): Servicio
    {
        return Servicio::create([
            'tipo' => $tipo, 'titulo' => 'Thai Fusión', 'descripcion' => 'Masaje de prueba',
            'duracion' => '60 min', 'precio' => '90 €', 'orden' => 1,
        ]);
    }

    private function cliente(bool $conCuestionario = true): User
    {
        return User::factory()->create($conCuestionario ? [
            'sexo' => 'femenino', 'objetivo' => 'dolor', 'frecuencia' => 'mensual',
            'telefono' => '600000000', 'perfil_completado_at' => now(),
        ] : []);
    }

    public function test_la_portada_tiene_el_boton_reservar_servicio(): void
    {
        $this->get('/')->assertOk()->assertSee('Reservar servicio')->assertDontSee('Reservar sesión');
    }

    public function test_sin_sesion_se_ofrece_registrarse(): void
    {
        $servicio = $this->servicio();

        $this->get(route('reservar', $servicio))
            ->assertOk()
            ->assertSee('Registrarme')
            ->assertSee(route('register'));
    }

    public function test_tras_registrarse_responde_el_cuestionario_y_vuelve_al_servicio(): void
    {
        $servicio = $this->servicio();

        // Elige el servicio sin sesión y se registra
        $this->get(route('reservar', $servicio));
        $this->post('/register', [
            'name' => 'Ana', 'email' => 'ana@example.com',
            'password' => 'password', 'password_confirmation' => 'password',
        ])->assertRedirect(route('cuestionario'));

        // Sin elegir las opciones no se puede terminar
        $this->post(route('cuestionario.store'), ['telefono' => '600000000'])
            ->assertSessionHasErrors(['sexo', 'objetivo', 'frecuencia']);

        // Responde y vuelve a la reserva del servicio que había elegido
        $this->post(route('cuestionario.store'), [
            'sexo' => 'femenino', 'objetivo' => 'relajacion', 'frecuencia' => 'semanal', 'telefono' => '600000000',
        ])->assertRedirect(route('reservar', $servicio));

        $ana = User::where('email', 'ana@example.com')->first();
        $this->assertSame('relajacion', $ana->objetivo);
        $this->assertNotNull($ana->perfil_completado_at);

        // El cuestionario no se vuelve a preguntar
        $this->get(route('cuestionario'))->assertRedirect();
    }

    public function test_un_cliente_con_cuestionario_solicita_un_curso(): void
    {
        // Los cursos no usan la agenda: se envía una solicitud que confirma el administrador
        $servicio = $this->servicio('cursos');
        $cliente = $this->cliente();

        $this->actingAs($cliente)->get(route('reservar', $servicio))->assertOk()->assertSee('Enviar solicitud');

        $this->actingAs($cliente)
            ->post(route('reservar.store', $servicio), ['telefono' => '611111111', 'notas' => 'Por la tarde'])
            ->assertRedirect(route('citas'));

        $this->assertDatabaseHas('citas', [
            'user_id' => $cliente->id, 'servicio_id' => $servicio->id, 'estado' => 'pendiente', 'notas' => 'Por la tarde',
        ]);
    }

    public function test_sin_cuestionario_no_se_puede_reservar(): void
    {
        $servicio = $this->servicio();
        $cliente = $this->cliente(false);

        $this->actingAs($cliente)->get(route('reservar', $servicio))->assertRedirect(route('cuestionario'));
        $this->actingAs($cliente)->post(route('reservar.store', $servicio), ['telefono' => '6'])->assertRedirect(route('cuestionario'));
        $this->assertSame(0, Cita::count());
    }

    public function test_el_admin_entra_con_su_usuario_y_ve_el_panel(): void
    {
        User::factory()->create(['username' => 'admin', 'password' => 'secreta', 'is_admin' => true]);

        $this->post('/login', ['email' => 'admin', 'password' => 'secreta'])->assertRedirect(route('admin.dashboard'));

        $this->get(route('admin.dashboard'))->assertOk()->assertSee('Clientes registrados');
        $this->get(route('admin.clientes.index'))->assertOk();
        $this->get(route('admin.servicios.index'))->assertOk();
        $this->get(route('admin.servicios.create'))->assertOk();
    }

    public function test_un_cliente_no_entra_al_panel(): void
    {
        $this->actingAs($this->cliente())->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_el_admin_gestiona_servicios_con_imagen_en_la_base_de_datos(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->post(route('admin.servicios.store'), [
            'tipo' => 'yoga', 'titulo' => 'Pack Nuevo', 'descripcion' => 'Descripción',
            'duracion' => '8 sesiones/mes', 'precio' => '160 €/mes', 'orden' => 3,
            'imagen_archivo' => UploadedFile::fake()->image('foto.jpg', 400, 300),
        ])->assertRedirect(route('admin.servicios.index'));

        $servicio = Servicio::where('titulo', 'Pack Nuevo')->first();
        $this->assertNotNull($servicio->imagen_actualizada_at);
        $this->assertDatabaseHas('servicio_imagenes', ['servicio_id' => $servicio->id, 'mime' => 'image/jpeg']);

        // La imagen se sirve desde la base de datos
        $this->get(route('servicios.imagen', $servicio))->assertOk()->assertHeader('Content-Type', 'image/jpeg');

        // Editar y eliminar
        $this->actingAs($admin)->put(route('admin.servicios.update', $servicio), [
            'tipo' => 'yoga', 'titulo' => 'Pack Renovado', 'descripcion' => 'Otra',
            'duracion' => '12 sesiones/mes', 'precio' => '219 €/mes', 'orden' => 1,
        ])->assertRedirect(route('admin.servicios.index'));
        $this->assertSame('Pack Renovado', $servicio->fresh()->titulo);

        $this->actingAs($admin)->delete(route('admin.servicios.destroy', $servicio));
        $this->assertDatabaseMissing('servicios', ['id' => $servicio->id]);
        $this->assertDatabaseMissing('servicio_imagenes', ['servicio_id' => $servicio->id]);
    }

    public function test_el_admin_cambia_el_estado_de_una_cita(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $cliente = $this->cliente();
        $cita = Cita::create(['user_id' => $cliente->id, 'nombre' => 'X', 'telefono' => '6', 'estado' => 'pendiente']);

        $this->actingAs($admin)->get(route('admin.clientes.show', $cliente))->assertOk();
        $this->actingAs($admin)->patch(route('admin.citas.update', $cita), ['estado' => 'confirmada']);

        $this->assertSame('confirmada', $cita->fresh()->estado);
    }
}
