<?php

namespace Tests\Feature;

use App\Models\Bono;
use App\Models\Cita;
use App\Models\Pago;
use App\Models\Servicio;
use App\Models\User;
use App\Services\Agenda;
use App\Services\Pagos;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Pasarela simulada: imita las respuestas de Stripe sin conectarse a internet. */
class PagosFalso extends Pagos
{
    public array $sesiones = [];
    public array $reembolsos = [];
    public string $estadoPago = 'paid';
    public bool $fallarReembolso = false;

    protected function stripeCrearSesion(array $datos): object
    {
        $this->sesiones[] = $datos;
        $id = 'cs_test_' . count($this->sesiones);

        return (object) ['id' => $id, 'url' => 'https://checkout.stripe.com/c/pay/' . $id];
    }

    protected function stripeLeerSesion(string $id): object
    {
        return (object) ['payment_status' => $this->estadoPago, 'payment_intent' => 'pi_' . $id];
    }

    protected function stripeReembolsar(string $paymentIntent): void
    {
        if ($this->fallarReembolso) {
            throw new \RuntimeException('Stripe no disponible');
        }
        $this->reembolsos[] = $paymentIntent;
    }
}

/** Pagos con Stripe: masajes al reservar, packs de yoga con bono y cursos tras la aceptación. */
class PagosTest extends TestCase
{
    use RefreshDatabase;

    private PagosFalso $stripe;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::create(2026, 11, 2, 8, 0)); // lunes
        config(['services.stripe.secret' => 'sk_test_simulada']);

        $this->stripe = new PagosFalso();
        $this->app->instance(Pagos::class, $this->stripe);

        app(Agenda::class)->abrirMes(Carbon::create(2026, 11, 1));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function cliente(): User
    {
        return User::factory()->create([
            'sexo' => 'femenino', 'objetivo' => 'dolor', 'frecuencia' => 'mensual',
            'telefono' => '600000000', 'perfil_completado_at' => now(),
        ]);
    }

    private function servicio(string $tipo, string $duracion, string $precio): Servicio
    {
        return Servicio::create(['tipo' => $tipo, 'titulo' => ucfirst($tipo), 'descripcion' => 'x', 'duracion' => $duracion, 'precio' => $precio, 'orden' => 1]);
    }

    private function reservar(User $user, Servicio $servicio, string $inicio)
    {
        return $this->actingAs($user)->post(route('reservar.store', $servicio), ['inicio' => $inicio, 'telefono' => '600000000']);
    }

    private function volverDeStripe(User $user, Pago $pago)
    {
        return $this->actingAs($user)->get(route('pagos.exito', $pago) . '?session_id=' . $pago->fresh()->stripe_session_id);
    }

    private function horas(Servicio $servicio, string $dia): array
    {
        return $this->actingAs($this->cliente())->getJson(route('reservar.disponibilidad', $servicio))->json($dia) ?? [];
    }

    // ──────────────── Masajes ────────────────

    public function test_al_reservar_un_masaje_se_va_a_pagar_y_la_hora_queda_apartada(): void
    {
        $masaje = $this->servicio('masajes', '60 min', '90 €');
        $ana = $this->cliente();

        $this->reservar($ana, $masaje, '2026-11-03 10:00')->assertRedirect('https://checkout.stripe.com/c/pay/cs_test_1');

        $cita = Cita::first();
        $this->assertSame('pendiente', $cita->estado);
        $this->assertSame(9000, $cita->pago->importe);
        $this->assertSame('eur', $this->stripe->sesiones[0]['line_items'][0]['price_data']['currency']);
        $this->assertSame(9000, $this->stripe->sesiones[0]['line_items'][0]['price_data']['unit_amount']);
        $this->assertNotContains('10:00', $this->horas($masaje, '2026-11-03'));
    }

    public function test_al_volver_de_stripe_pagado_la_cita_se_confirma(): void
    {
        $masaje = $this->servicio('masajes', '60 min', '90 €');
        $ana = $this->cliente();
        $this->reservar($ana, $masaje, '2026-11-03 10:00');
        $pago = Pago::first();

        $this->volverDeStripe($ana, $pago)->assertRedirect(route('citas'))->assertSessionHas('success');

        $this->assertSame('pagado', $pago->fresh()->estado);
        $this->assertSame('confirmada', Cita::first()->estado);
        $this->assertTrue(Cita::first()->tieneQr());
    }

    public function test_no_se_confirma_sin_pago_real_ni_con_otra_sesion(): void
    {
        $masaje = $this->servicio('masajes', '60 min', '90 €');
        $ana = $this->cliente();
        $this->reservar($ana, $masaje, '2026-11-03 10:00');
        $pago = Pago::first();

        $this->actingAs($ana)->get(route('pagos.exito', $pago) . '?session_id=cs_inventada')->assertSessionHas('error');
        $this->stripe->estadoPago = 'unpaid';
        $this->volverDeStripe($ana, $pago)->assertSessionHas('error');

        $this->assertSame('pendiente', Cita::first()->estado);
        $this->actingAs($this->cliente())->get(route('pagos.exito', $pago))->assertForbidden();
    }

    public function test_si_cancela_el_pago_la_hora_se_libera(): void
    {
        $masaje = $this->servicio('masajes', '60 min', '90 €');
        $ana = $this->cliente();
        $this->reservar($ana, $masaje, '2026-11-03 10:00');

        $this->actingAs($ana)->get(route('pagos.cancelado', Pago::first()))->assertRedirect(route('citas'));

        $this->assertSame('cancelada', Cita::first()->estado);
        $this->assertSame('cancelado', Pago::first()->estado);
        $this->assertContains('10:00', $this->horas($masaje, '2026-11-03'));
    }

    public function test_un_pago_abandonado_se_libera_al_pasar_el_tiempo(): void
    {
        $masaje = $this->servicio('masajes', '60 min', '90 €');
        $this->reservar($this->cliente(), $masaje, '2026-11-03 10:00');

        Carbon::setTestNow(now()->addMinutes(Pagos::MINUTOS_PARA_PAGAR + 10));

        $this->assertContains('10:00', $this->horas($masaje, '2026-11-03'));
        $this->assertSame('cancelada', Cita::first()->estado);
    }

    public function test_se_puede_completar_un_pago_a_medias(): void
    {
        $masaje = $this->servicio('masajes', '60 min', '90 €');
        $ana = $this->cliente();
        $this->reservar($ana, $masaje, '2026-11-03 10:00');

        $this->actingAs($ana)->get(route('citas'))->assertSee('Completar pago');
        $this->actingAs($ana)->post(route('citas.pagar', Cita::first()))->assertRedirect('https://checkout.stripe.com/c/pay/cs_test_2');
        $this->assertSame(1, Pago::count());
    }

    public function test_el_descuento_de_nivel_se_aplica_al_cobro(): void
    {
        $masaje = $this->servicio('masajes', '60 min', '90 €');
        $ana = $this->cliente();
        foreach (range(1, 11) as $i) {
            Cita::create(['user_id' => $ana->id, 'nombre' => 'Ana', 'telefono' => '6', 'estado' => 'confirmada']);
        }

        $this->reservar($ana, $masaje, '2026-11-03 10:00');

        $this->assertSame(8100, Pago::first()->importe); // 90 € − 10 % (Bronze)
    }

    // ──────────────── Reembolsos ────────────────

    public function test_al_cancelar_una_cita_pagada_se_devuelve_el_dinero(): void
    {
        $masaje = $this->servicio('masajes', '60 min', '90 €');
        $ana = $this->cliente();
        $this->reservar($ana, $masaje, '2026-11-05 10:00');
        $this->volverDeStripe($ana, Pago::first());

        $this->actingAs($ana)->post(route('citas.cancelar', Cita::first()))->assertSessionHas('success');

        $this->assertSame('reembolsado', Pago::first()->estado);
        $this->assertSame(['pi_cs_test_1'], $this->stripe->reembolsos);
        $this->assertContains('10:00', $this->horas($masaje, '2026-11-05'));
    }

    public function test_si_falla_la_devolucion_se_avisa(): void
    {
        $masaje = $this->servicio('masajes', '60 min', '90 €');
        $ana = $this->cliente();
        $this->reservar($ana, $masaje, '2026-11-05 10:00');
        $this->volverDeStripe($ana, Pago::first());
        $this->stripe->fallarReembolso = true;

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->patch(route('admin.citas.update', Cita::first()), ['estado' => 'cancelada'])->assertSessionHas('error');

        $this->assertSame('cancelada', Cita::first()->estado);
        $this->assertSame('pagado', Pago::first()->estado);
    }

    // ──────────────── Yoga: pack completo y bono ────────────────

    public function test_la_primera_clase_de_yoga_cobra_el_pack_y_crea_el_bono(): void
    {
        $yoga = $this->servicio('yoga', '8 sesiones/mes', '160 €/mes');
        $ana = $this->cliente();

        $this->reservar($ana, $yoga, '2026-11-03 10:00')->assertRedirect();
        $this->assertSame(16000, Pago::first()->importe);
        $this->assertSame('pendiente', Bono::first()->estado);

        $this->volverDeStripe($ana, Pago::first());

        $bono = Bono::first();
        $this->assertSame('activo', $bono->estado);
        $this->assertSame(8, $bono->sesiones_totales);
        $this->assertSame(7, $bono->sesionesLibres()); // una ya reservada
    }

    public function test_las_siguientes_clases_van_contra_el_bono_sin_pagar(): void
    {
        $yoga = $this->servicio('yoga', '8 sesiones/mes', '160 €/mes');
        $ana = $this->cliente();
        $this->reservar($ana, $yoga, '2026-11-03 10:00');
        $this->volverDeStripe($ana, Pago::first());

        $this->reservar($ana, $yoga, '2026-11-04 10:00')->assertRedirect(route('citas'))->assertSessionHas('success');

        $segunda = Cita::latest('id')->first();
        $this->assertSame('confirmada', $segunda->estado);
        $this->assertSame(Bono::first()->id, $segunda->bono_id);
        $this->assertSame(1, Pago::count()); // no se ha cobrado otra vez
    }

    public function test_al_validar_el_qr_se_resta_una_sesion_del_bono(): void
    {
        $yoga = $this->servicio('yoga', '8 sesiones/mes', '160 €/mes');
        $ana = $this->cliente();
        $this->reservar($ana, $yoga, '2026-11-03 10:00');
        $this->volverDeStripe($ana, Pago::first());
        $cita = Cita::first();
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get(route('admin.citas.validar', ['cita' => $cita, 'codigo' => $cita->codigo]))
            ->assertSee('Quedan 8 de 8 sesiones')->assertSee('Al validar quedarán 7');
        $this->actingAs($admin)->post(route('admin.citas.validar.store', ['cita' => $cita, 'codigo' => $cita->codigo]));

        $this->assertSame(7, Bono::first()->sesionesRestantes());
    }

    public function test_con_el_bono_agotado_se_vuelve_a_cobrar_el_pack(): void
    {
        $yoga = $this->servicio('yoga', '4 sesiones', '200 €');
        $ana = $this->cliente();
        Bono::create(['user_id' => $ana->id, 'servicio_id' => $yoga->id, 'sesiones_totales' => 4, 'sesiones_usadas' => 4, 'estado' => 'activo']);

        $this->reservar($ana, $yoga, '2026-11-03 10:00')->assertRedirect('https://checkout.stripe.com/c/pay/cs_test_1');
        $this->assertSame(20000, Pago::first()->importe);
    }

    public function test_cancelar_una_clase_del_bono_no_devuelve_dinero_y_la_sesion_vuelve_al_bono(): void
    {
        $yoga = $this->servicio('yoga', '8 sesiones/mes', '160 €/mes');
        $ana = $this->cliente();
        $this->reservar($ana, $yoga, '2026-11-05 10:00');
        $this->volverDeStripe($ana, Pago::first());

        $this->actingAs($ana)->post(route('citas.cancelar', Cita::first()));

        $this->assertSame([], $this->stripe->reembolsos);
        $this->assertSame('pagado', Pago::first()->estado);
        $this->assertSame(8, Bono::first()->sesionesLibres());
    }

    // ──────────────── Cursos ────────────────

    public function test_el_curso_se_paga_cuando_la_administradora_lo_acepta(): void
    {
        $curso = $this->servicio('cursos', '30 h', '840 €');
        $ana = $this->cliente();

        // Solicitud sin pago
        $this->actingAs($ana)->post(route('reservar.store', $curso), ['telefono' => '6'])->assertRedirect(route('citas'));
        $cita = Cita::first();
        $this->assertSame('pendiente', $cita->estado);
        $this->assertSame(0, Pago::count());
        $this->actingAs($ana)->get(route('citas'))->assertDontSee('Pagar</button>', false);

        // La administradora la acepta
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->patch(route('admin.citas.update', $cita), ['estado' => 'confirmada']);

        $this->actingAs($ana)->get(route('citas'))->assertSee('Ya puedes pagar el curso');
        $this->actingAs($ana)->post(route('citas.pagar', $cita))->assertRedirect('https://checkout.stripe.com/c/pay/cs_test_1');
        $this->assertSame(84000, Pago::first()->importe);

        $this->volverDeStripe($ana, Pago::first());
        $this->assertSame('pagado', Pago::first()->estado);
        $this->assertFalse($cita->fresh()->cursoPorPagar());
    }

    public function test_sin_clave_de_stripe_se_reserva_sin_pagar(): void
    {
        config(['services.stripe.secret' => null]);
        $masaje = $this->servicio('masajes', '60 min', '90 €');

        $this->reservar($this->cliente(), $masaje, '2026-11-03 10:00')->assertRedirect(route('citas'));

        $this->assertSame('confirmada', Cita::first()->estado);
        $this->assertSame(0, Pago::count());
    }

    // ──────────────── Webhook de Stripe ────────────────

    /** Envía un aviso de Stripe firmado como lo firma Stripe (HMAC-SHA256 con el secreto del webhook). */
    private function avisoDeStripe(string $tipo, Pago $pago, string $estadoPago = 'paid', ?string $secreto = 'whsec_prueba')
    {
        config(['services.stripe.webhook_secret' => 'whsec_prueba']);

        $payload = json_encode([
            'id' => 'evt_test', 'object' => 'event', 'type' => $tipo,
            'data' => ['object' => [
                'id' => $pago->fresh()->stripe_session_id, 'object' => 'checkout.session',
                'payment_status' => $estadoPago, 'payment_intent' => 'pi_webhook',
                'metadata' => ['pago_id' => (string) $pago->id],
            ]],
        ]);
        $t = time();
        $firma = hash_hmac('sha256', $t . '.' . $payload, $secreto);

        return $this->call('POST', route('stripe.webhook'), [], [], [], [
            'HTTP_STRIPE_SIGNATURE' => "t={$t},v1={$firma}",
            'CONTENT_TYPE' => 'application/json',
        ], $payload);
    }

    public function test_el_webhook_confirma_el_pago_aunque_el_cliente_cierre_la_pestana(): void
    {
        $masaje = $this->servicio('masajes', '60 min', '90 €');
        $this->reservar($this->cliente(), $masaje, '2026-11-03 10:00');

        // El cliente paga y cierra la pestaña: no vuelve a la web, pero Stripe avisa
        $this->avisoDeStripe('checkout.session.completed', Pago::first())->assertOk();

        $this->assertSame('pagado', Pago::first()->estado);
        $this->assertSame('pi_webhook', Pago::first()->stripe_payment_intent);
        $this->assertSame('confirmada', Cita::first()->estado);

        // Pasado el tiempo de pago la cita no se libera, porque ya está pagada
        Carbon::setTestNow(now()->addHour());
        $this->assertNotContains('10:00', $this->horas($masaje, '2026-11-03'));
    }

    public function test_el_webhook_y_la_vuelta_del_cliente_no_confirman_dos_veces(): void
    {
        $yoga = $this->servicio('yoga', '8 sesiones/mes', '160 €/mes');
        $ana = $this->cliente();
        $this->reservar($ana, $yoga, '2026-11-03 10:00');

        $this->avisoDeStripe('checkout.session.completed', Pago::first())->assertOk();
        $this->avisoDeStripe('checkout.session.completed', Pago::first())->assertOk();   // Stripe puede repetir avisos
        $this->volverDeStripe($ana, Pago::first())->assertSessionHas('success');

        $this->assertSame('activo', Bono::first()->estado);
        $this->assertSame(1, Pago::count());
        $this->assertSame([], $this->stripe->reembolsos);
    }

    public function test_un_aviso_con_firma_falsa_se_rechaza(): void
    {
        $masaje = $this->servicio('masajes', '60 min', '90 €');
        $this->reservar($this->cliente(), $masaje, '2026-11-03 10:00');

        $this->avisoDeStripe('checkout.session.completed', Pago::first(), secreto: 'whsec_inventado')->assertStatus(400);

        $this->assertSame('pendiente', Pago::first()->estado);
    }

    public function test_si_la_sesion_de_pago_caduca_se_libera_la_hora(): void
    {
        $masaje = $this->servicio('masajes', '60 min', '90 €');
        $this->reservar($this->cliente(), $masaje, '2026-11-03 10:00');

        $this->avisoDeStripe('checkout.session.expired', Pago::first(), 'unpaid')->assertOk();

        $this->assertSame('cancelada', Cita::first()->estado);
        $this->assertContains('10:00', $this->horas($masaje, '2026-11-03'));
    }

    public function test_un_pago_que_llega_tarde_se_devuelve(): void
    {
        $masaje = $this->servicio('masajes', '60 min', '90 €');
        $ana = $this->cliente();
        $this->reservar($ana, $masaje, '2026-11-03 10:00');
        $this->actingAs($ana)->get(route('pagos.cancelado', Pago::first()));   // la reserva ya se anuló

        $this->avisoDeStripe('checkout.session.completed', Pago::first())->assertOk();

        $this->assertSame('reembolsado', Pago::first()->estado);
        $this->assertSame(['pi_webhook'], $this->stripe->reembolsos);
        $this->assertSame('cancelada', Cita::first()->estado);
    }

    public function test_sin_secreto_del_webhook_la_ruta_no_existe(): void
    {
        config(['services.stripe.webhook_secret' => null]);

        $this->post(route('stripe.webhook'), [])->assertNotFound();
    }
}
