<?php

namespace Tests\Feature;

use App\Models\Ajuste;
use App\Models\Cita;
use App\Models\Franja;
use App\Models\Servicio;
use App\Models\SolicitudCambio;
use App\Models\User;
use App\Services\Agenda;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Agenda: abrir meses, reservar horas, cambios, cancelaciones y niveles de fidelidad. */
class AgendaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Lunes 2 de noviembre de 2026, 8:00
        Carbon::setTestNow(Carbon::create(2026, 11, 2, 8, 0));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function masaje(string $duracion = '60 min'): Servicio
    {
        return Servicio::create(['tipo' => 'masajes', 'titulo' => 'Masaje', 'descripcion' => 'Texto', 'duracion' => $duracion, 'precio' => '90 €', 'orden' => 1]);
    }

    private function cliente(): User
    {
        return User::factory()->create([
            'sexo' => 'femenino', 'objetivo' => 'dolor', 'frecuencia' => 'mensual',
            'telefono' => '600000000', 'perfil_completado_at' => now(),
        ]);
    }

    private function abrirNoviembre(): void
    {
        app(Agenda::class)->abrirMes(Carbon::create(2026, 11, 1));
    }

    private function disponibles(User $user, Servicio $servicio): array
    {
        return $this->actingAs($user)->getJson(route('reservar.disponibilidad', $servicio))->assertOk()->json();
    }

    // ──────────────── Abrir meses ────────────────

    public function test_abrir_un_mes_crea_las_horas_segun_el_horario(): void
    {
        $this->abrirNoviembre();

        // 21 días de lunes a viernes × 9 horas + 4 sábados × 5 horas
        $this->assertSame(21 * 9 + 4 * 5, Franja::count());

        $lunes = Franja::whereDate('inicio', '2026-11-02')->orderBy('inicio')->get()->map(fn ($f) => $f->inicio->format('H:i'))->all();
        $this->assertSame(['10:00', '11:00', '12:00', '13:00', '16:00', '17:00', '18:00', '19:00', '20:00'], $lunes);

        $sabado = Franja::whereDate('inicio', '2026-11-07')->orderBy('inicio')->get()->map(fn ($f) => $f->inicio->format('H:i'))->all();
        $this->assertSame(['09:00', '10:00', '11:00', '12:00', '13:00'], $sabado); // sábado de 09:00 a 14:00

        $this->assertSame(0, Franja::whereDate('inicio', '2026-11-08')->count()); // domingo
    }

    public function test_abrir_dos_veces_no_duplica_horas(): void
    {
        $this->abrirNoviembre();
        $this->abrirNoviembre();

        $this->assertSame(21 * 9 + 4 * 5, Franja::count());
    }

    public function test_el_admin_abre_el_mes_desde_el_panel(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->post(route('admin.agenda.abrir'), ['mes' => '2026-11'])->assertRedirect();
        $this->assertTrue(Franja::exists());

        $this->actingAs($admin)->get(route('admin.agenda', ['mes' => '2026-11']))->assertOk()->assertSee('10:00');
        $this->actingAs($this->cliente())->post(route('admin.agenda.abrir'), ['mes' => '2026-12'])->assertForbidden();
    }

    public function test_el_horario_de_la_configuracion_se_usa_al_abrir_el_mes(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $horario = [];
        foreach (range(1, 7) as $dia) {
            $horario[$dia] = [['apertura' => $dia <= 5 ? '09:00' : '', 'cierre' => $dia <= 5 ? '11:00' : ''], ['apertura' => '', 'cierre' => '']];
        }

        $this->actingAs($admin)->put(route('admin.configuracion.horario'), ['horario' => $horario])->assertRedirect();
        $this->abrirNoviembre();

        // Solo de lunes a viernes, a las 9 y a las 10
        $this->assertSame(21 * 2, Franja::count());
        $this->assertSame([], Ajuste::horario()[6]);
    }

    // ──────────────── Reservar ────────────────

    public function test_un_invitado_no_puede_ver_las_horas(): void
    {
        $servicio = $this->masaje();
        $this->get(route('reservar.disponibilidad', $servicio))->assertRedirect(route('login'));
    }

    public function test_el_cliente_reserva_una_hora_y_deja_de_estar_disponible(): void
    {
        $this->abrirNoviembre();
        $servicio = $this->masaje();
        $ana = $this->cliente();

        $this->assertContains('10:00', $this->disponibles($ana, $servicio)['2026-11-03']);

        $this->actingAs($ana)->post(route('reservar.store', $servicio), [
            'inicio' => '2026-11-03 10:00', 'telefono' => '611111111',
        ])->assertRedirect(route('citas'));

        $cita = Cita::first();
        $this->assertSame('confirmada', $cita->estado);
        $this->assertSame('2026-11-03 10:00', $cita->inicio->format('Y-m-d H:i'));
        $this->assertSame('2026-11-03 11:00', $cita->fin->format('Y-m-d H:i'));

        // Otro cliente ya no ve esa hora y no puede reservarla
        $luis = $this->cliente();
        $this->assertNotContains('10:00', $this->disponibles($luis, $servicio)['2026-11-03']);
        $this->actingAs($luis)->post(route('reservar.store', $servicio), [
            'inicio' => '2026-11-03 10:00', 'telefono' => '622222222',
        ])->assertSessionHasErrors('inicio');
        $this->assertSame(1, Cita::count());
    }

    public function test_un_masaje_largo_ocupa_las_horas_que_necesita(): void
    {
        $this->abrirNoviembre();
        $largo = $this->masaje('95 min');
        $ana = $this->cliente();

        // 2 horas: a las 13:00 no cabe (se cierra a las 14:00) y a las 20:00 tampoco
        $horas = $this->disponibles($ana, $largo)['2026-11-03'];
        $this->assertSame(['10:00', '11:00', '12:00', '16:00', '17:00', '18:00', '19:00'], $horas);

        $this->actingAs($ana)->post(route('reservar.store', $largo), ['inicio' => '2026-11-03 11:00', 'telefono' => '6']);
        $this->assertSame(2, Cita::first()->ocupaciones()->count());

        // Las 11:00 y las 12:00 quedan ocupadas para un masaje de una hora
        $horas = $this->disponibles($this->cliente(), $this->masaje())['2026-11-03'];
        $this->assertNotContains('11:00', $horas);
        $this->assertNotContains('12:00', $horas);
        $this->assertContains('10:00', $horas);
    }

    public function test_una_hora_bloqueada_no_se_ofrece(): void
    {
        $this->abrirNoviembre();
        $admin = User::factory()->create(['is_admin' => true]);
        $franja = Franja::where('inicio', '2026-11-03 10:00:00')->first();

        $this->actingAs($admin)->post(route('admin.agenda.bloquear', $franja));
        $this->assertNotContains('10:00', $this->disponibles($this->cliente(), $this->masaje())['2026-11-03']);

        $this->actingAs($admin)->post(route('admin.agenda.bloquear', $franja));
        $this->assertContains('10:00', $this->disponibles($this->cliente(), $this->masaje())['2026-11-03']);
    }

    public function test_no_se_ofrecen_horas_pasadas(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 11, 3, 12, 30));
        app(Agenda::class)->abrirMes(Carbon::create(2026, 11, 1));

        $horas = $this->disponibles($this->cliente(), $this->masaje())['2026-11-03'];
        $this->assertSame(['13:00', '16:00', '17:00', '18:00', '19:00', '20:00'], $horas);
    }

    // ──────────────── Cambios y cancelaciones ────────────────

    public function test_el_cambio_de_cita_lo_aprueba_el_administrador(): void
    {
        $this->abrirNoviembre();
        $servicio = $this->masaje();
        $ana = $this->cliente();
        $this->actingAs($ana)->post(route('reservar.store', $servicio), ['inicio' => '2026-11-03 10:00', 'telefono' => '6']);
        $cita = Cita::first();

        $this->actingAs($ana)->post(route('citas.cambio', $cita), ['inicio' => '2026-11-05 17:00'])->assertSessionHasNoErrors();

        // La hora nueva queda apartada y la antigua sigue ocupada hasta que el admin decida
        $otro = $this->cliente();
        $this->assertNotContains('17:00', $this->disponibles($otro, $servicio)['2026-11-05']);
        $this->assertNotContains('10:00', $this->disponibles($otro, $servicio)['2026-11-03']);

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->post(route('admin.solicitudes.aprobar', SolicitudCambio::first()))->assertRedirect();

        $this->assertSame('2026-11-05 17:00', $cita->fresh()->inicio->format('Y-m-d H:i'));
        $this->assertContains('10:00', $this->disponibles($otro, $servicio)['2026-11-03']);
        $this->assertSame(1, $cita->ocupaciones()->count());
    }

    public function test_si_el_admin_rechaza_el_cambio_la_cita_sigue_igual(): void
    {
        $this->abrirNoviembre();
        $servicio = $this->masaje();
        $ana = $this->cliente();
        $this->actingAs($ana)->post(route('reservar.store', $servicio), ['inicio' => '2026-11-03 10:00', 'telefono' => '6']);
        $cita = Cita::first();
        $this->actingAs($ana)->post(route('citas.cambio', $cita), ['inicio' => '2026-11-05 17:00']);

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->post(route('admin.solicitudes.rechazar', SolicitudCambio::first()));

        $this->assertSame('2026-11-03 10:00', $cita->fresh()->inicio->format('Y-m-d H:i'));
        $this->assertContains('17:00', $this->disponibles($this->cliente(), $servicio)['2026-11-05']);
        $this->assertSame('rechazada', SolicitudCambio::first()->estado);
    }

    public function test_un_cliente_no_puede_tocar_citas_de_otro(): void
    {
        $this->abrirNoviembre();
        $servicio = $this->masaje();
        $ana = $this->cliente();
        $this->actingAs($ana)->post(route('reservar.store', $servicio), ['inicio' => '2026-11-03 10:00', 'telefono' => '6']);

        $luis = $this->cliente();
        $this->actingAs($luis)->post(route('citas.cancelar', Cita::first()))->assertForbidden();
        $this->actingAs($luis)->post(route('citas.cambio', Cita::first()), ['inicio' => '2026-11-05 17:00'])->assertForbidden();
    }

    public function test_el_cliente_cancela_con_24_horas_y_se_libera_la_hora(): void
    {
        $this->abrirNoviembre();
        $servicio = $this->masaje();
        $ana = $this->cliente();
        $this->actingAs($ana)->post(route('reservar.store', $servicio), ['inicio' => '2026-11-05 10:00', 'telefono' => '6']);

        $this->actingAs($ana)->post(route('citas.cancelar', Cita::first()))->assertSessionHasNoErrors();

        $this->assertSame('cancelada', Cita::first()->estado);
        $this->assertContains('10:00', $this->disponibles($this->cliente(), $servicio)['2026-11-05']);
    }

    public function test_con_menos_de_24_horas_el_cliente_no_puede_cancelar(): void
    {
        $this->abrirNoviembre();
        $servicio = $this->masaje();
        $ana = $this->cliente();
        $this->actingAs($ana)->post(route('reservar.store', $servicio), ['inicio' => '2026-11-02 16:00', 'telefono' => '6']);

        $this->actingAs($ana)->post(route('citas.cancelar', Cita::first()))->assertSessionHas('error');
        $this->assertSame('confirmada', Cita::first()->estado);
    }

    public function test_el_admin_cancela_una_cita_y_se_libera_la_hora(): void
    {
        $this->abrirNoviembre();
        $servicio = $this->masaje();
        $this->actingAs($this->cliente())->post(route('reservar.store', $servicio), ['inicio' => '2026-11-02 16:00', 'telefono' => '6']);

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->patch(route('admin.citas.update', Cita::first()), ['estado' => 'cancelada']);

        $this->assertSame('cancelada', Cita::first()->estado);
        $this->assertContains('16:00', $this->disponibles($this->cliente(), $servicio)['2026-11-02']);
    }

    // ──────────────── Niveles de fidelidad ────────────────

    public function test_niveles_de_fidelidad(): void
    {
        $user = new User();

        $this->assertNull($user->nivel(10));
        $this->assertSame(['clave' => 'bronze', 'nombre' => 'Bronze', 'descuento' => 10], $user->nivel(11));
        $this->assertSame('silver', $user->nivel(21)['clave']);
        $this->assertSame(15, $user->nivel(21)['descuento']);
        $this->assertSame('gold', $user->nivel(31)['clave']);
        $this->assertSame(20, $user->nivel(31)['descuento']);
        $this->assertSame(['nombre' => 'Bronze', 'faltan' => 1, 'descuento' => 10], $user->siguienteNivel(10));
        $this->assertNull($user->siguienteNivel(31));
    }

    public function test_un_cliente_bronze_reserva_con_descuento(): void
    {
        $this->abrirNoviembre();
        $servicio = $this->masaje();
        $ana = $this->cliente();

        // 11 sesiones anteriores (las canceladas no cuentan)
        foreach (range(1, 11) as $i) {
            Cita::create(['user_id' => $ana->id, 'nombre' => 'Ana', 'telefono' => '6', 'estado' => 'confirmada']);
        }
        Cita::create(['user_id' => $ana->id, 'nombre' => 'Ana', 'telefono' => '6', 'estado' => 'cancelada']);

        $this->actingAs($ana)->post(route('reservar.store', $servicio), ['inicio' => '2026-11-03 10:00', 'telefono' => '6']);

        $cita = Cita::whereNotNull('inicio')->first();
        $this->assertSame(10, $cita->descuento);
        $this->assertSame('81 €', $cita->precio_final);
    }

    public function test_mis_citas_muestra_el_nivel(): void
    {
        $ana = $this->cliente();
        foreach (range(1, 21) as $i) {
            Cita::create(['user_id' => $ana->id, 'nombre' => 'Ana', 'telefono' => '6', 'estado' => 'confirmada']);
        }

        $this->actingAs($ana)->get(route('citas'))->assertOk()->assertSee('Silver')->assertSee('21');
    }
}
