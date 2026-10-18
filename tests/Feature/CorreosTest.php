<?php

namespace Tests\Feature;

use App\Mail\BienvenidaMail;
use App\Mail\ReservaConfirmadaMail;
use App\Models\Bono;
use App\Models\Cita;
use App\Models\Pago;
use App\Models\Servicio;
use App\Models\SolicitudCambio;
use App\Models\User;
use App\Services\Agenda;
use App\Services\Pagos;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/** Correos de bienvenida y de reserva confirmada (con el QR). No se envía nada de verdad. */
class CorreosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::create(2026, 11, 2, 8, 0));
        Mail::fake();
        app(Agenda::class)->abrirMes(Carbon::create(2026, 11, 1));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function cliente(array $datos = []): User
    {
        return User::factory()->create($datos + [
            'name' => 'Ana López', 'sexo' => 'femenino', 'objetivo' => 'dolor', 'frecuencia' => 'mensual',
            'telefono' => '600000000', 'perfil_completado_at' => now(),
        ]);
    }

    private function masaje(): Servicio
    {
        return Servicio::create(['tipo' => 'masajes', 'titulo' => 'Thai Fusión', 'descripcion' => 'x', 'duracion' => '60 min', 'precio' => '90 €', 'orden' => 1]);
    }

    public function test_al_registrarse_recibe_la_bienvenida(): void
    {
        $this->post('/register', [
            'name' => 'Ana', 'email' => 'ana@example.com', 'password' => 'password', 'password_confirmation' => 'password',
        ]);

        Mail::assertSent(BienvenidaMail::class, fn ($mail) => $mail->hasTo('ana@example.com'));
    }

    public function test_al_reservar_sin_pago_recibe_la_confirmacion_con_el_qr(): void
    {
        $ana = $this->cliente();
        $this->actingAs($ana)->post(route('reservar.store', $this->masaje()), ['inicio' => '2026-11-03 10:00', 'telefono' => '6']);

        Mail::assertSent(ReservaConfirmadaMail::class, function (ReservaConfirmadaMail $mail) use ($ana) {
            return $mail->hasTo($ana->email)
                && ! $mail->cambio
                && str_starts_with($mail->qrPng, "\x89PNG")                       // el QR es un PNG
                && collect($mail->attachments())->contains(fn ($a) => $a->as === 'codigo-qr-yoguifit.png'); // y va adjunto
        });
    }

    public function test_el_correo_enviado_lleva_los_datos_y_el_qr_incrustado(): void
    {
        $ana = $this->cliente();
        $this->actingAs($ana)->post(route('reservar.store', $this->masaje()), ['inicio' => '2026-11-03 10:00', 'telefono' => '6', 'notas' => 'Contractura']);

        // Envío real con el transporte «array»: el mensaje se construye entero (como saldría hacia Brevo) pero no sale
        // (Mail::fake() sustituye el gestor de correo, así que se crea uno real solo para este envío)
        $mailer = (new \Illuminate\Mail\MailManager(app()))->mailer('array');
        $mailer->to($ana)->send(new ReservaConfirmadaMail(Cita::first()));

        $mensaje = $mailer->getSymfonyTransport()->messages()->last()->getOriginalMessage();
        $html = $mensaje->getHtmlBody();

        $this->assertStringContainsString('Thai Fusión', $html);
        $this->assertStringContainsString('10:00', $html);
        $this->assertStringContainsString('Contractura', $html);
        $this->assertStringContainsString(route('citas'), $html);
        $this->assertMatchesRegularExpression('/<img src="cid:[^"]+"/', $html);   // QR incrustado (lo muestran Gmail y Outlook)
        $this->assertCount(3, $mensaje->getAttachments());                       // cabecera + QR incrustado + QR adjunto
        $this->assertSame(2, preg_match_all('/<img src="cid:/', $html));          // cabecera con el logo y QR
    }

    public function test_el_correo_sale_en_el_idioma_del_cliente(): void
    {
        $john = $this->cliente(['idioma' => 'en']);
        $this->actingAs($john)->post(route('reservar.store', $this->masaje()), ['inicio' => '2026-11-03 10:00', 'telefono' => '6']);

        Mail::assertSent(ReservaConfirmadaMail::class, fn ($mail) => $mail->locale === 'en');
        $this->assertSame('en', $john->preferredLocale());
    }

    public function test_con_pago_el_correo_llega_al_confirmarse_el_pago_y_solo_una_vez(): void
    {
        config(['services.stripe.secret' => 'sk_test_simulada']);
        $stripe = new PagosFalso();
        $this->app->instance(Pagos::class, $stripe);
        $ana = $this->cliente();

        $this->actingAs($ana)->post(route('reservar.store', $this->masaje()), ['inicio' => '2026-11-03 10:00', 'telefono' => '6']);
        Mail::assertNotSent(ReservaConfirmadaMail::class);   // todavía sin pagar

        $pago = Pago::first();
        $stripe->marcarPagado($pago, 'pi_1');                // webhook
        $stripe->marcarPagado($pago, 'pi_1');                // vuelta del cliente

        Mail::assertSent(ReservaConfirmadaMail::class, 1);
    }

    public function test_una_clase_con_bono_tambien_envia_la_confirmacion(): void
    {
        $yoga = Servicio::create(['tipo' => 'yoga', 'titulo' => 'Pack On', 'descripcion' => 'x', 'duracion' => '8 sesiones/mes', 'precio' => '160 €/mes', 'orden' => 1]);
        $ana = $this->cliente();
        Bono::create(['user_id' => $ana->id, 'servicio_id' => $yoga->id, 'sesiones_totales' => 8, 'estado' => 'activo']);

        $this->actingAs($ana)->post(route('reservar.store', $yoga), ['inicio' => '2026-11-03 10:00', 'telefono' => '6']);

        Mail::assertSent(ReservaConfirmadaMail::class, fn ($mail) => $mail->cita->bono_id !== null);
    }

    public function test_al_aprobar_un_cambio_llega_un_correo_con_los_nuevos_datos(): void
    {
        $ana = $this->cliente();
        $this->actingAs($ana)->post(route('reservar.store', $this->masaje()), ['inicio' => '2026-11-03 10:00', 'telefono' => '6']);
        $this->actingAs($ana)->post(route('citas.cambio', Cita::first()), ['inicio' => '2026-11-05 17:00']);

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->post(route('admin.solicitudes.aprobar', SolicitudCambio::first()));

        Mail::assertSent(ReservaConfirmadaMail::class, fn ($mail) => $mail->cambio && $mail->cita->inicio->format('H:i') === '17:00');
    }

    public function test_si_falla_el_envio_la_reserva_sigue_adelante(): void
    {
        // Servidor de correo que no existe: el envío falla de verdad
        config(['mail.default' => 'smtp', 'mail.mailers.smtp.host' => '127.0.0.1', 'mail.mailers.smtp.port' => 1]);
        Mail::swap(app('mail.manager'));
        $ana = $this->cliente();

        $this->actingAs($ana)->post(route('reservar.store', $this->masaje()), ['inicio' => '2026-11-03 10:00', 'telefono' => '6'])
            ->assertRedirect(route('citas'))
            ->assertSessionHas('success');

        $this->assertSame('confirmada', Cita::first()->estado);
    }
}
