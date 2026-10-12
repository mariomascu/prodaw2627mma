<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Servicio;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Código QR de las citas, validación por el administrador y página de niveles. */
class QrYNivelesTest extends TestCase
{
    use RefreshDatabase;

    private function citaConfirmada(?User $user = null, ?Carbon $inicio = null): Cita
    {
        $user ??= User::factory()->create(['name' => 'Ana López']);
        $servicio = Servicio::create(['tipo' => 'masajes', 'titulo' => 'Thai Fusión', 'descripcion' => 'x', 'duracion' => '60 min', 'precio' => '90 €', 'orden' => 1]);
        $inicio ??= now()->addDay()->setTime(10, 0);

        return Cita::create([
            'user_id' => $user->id, 'servicio_id' => $servicio->id, 'nombre' => $user->name, 'telefono' => '600111222',
            'estado' => 'confirmada', 'inicio' => $inicio, 'fin' => $inicio->copy()->addHour(), 'precio_final' => '90 €',
        ]);
    }

    public function test_cada_cita_tiene_un_codigo_secreto(): void
    {
        $cita = $this->citaConfirmada();

        $this->assertSame(32, strlen($cita->codigo));
        $this->assertStringContainsString($cita->codigo, $cita->urlValidacion());
    }

    public function test_el_cliente_ve_el_boton_del_qr(): void
    {
        $cita = $this->citaConfirmada();

        $this->actingAs($cita->user)->get(route('citas'))
            ->assertSee('Ver código QR')
            ->assertSee($cita->urlValidacion(), false);
    }

    public function test_el_admin_lee_el_qr_y_valida_la_sesion(): void
    {
        $cita = $this->citaConfirmada();
        $admin = User::factory()->create(['is_admin' => true]);
        $url = route('admin.citas.validar', ['cita' => $cita, 'codigo' => $cita->codigo]);

        $this->actingAs($admin)->get($url)->assertOk()->assertSee('Ana López')->assertSee('Thai Fusión')->assertSee('Validar sesión');

        $this->actingAs($admin)->post(route('admin.citas.validar.store', ['cita' => $cita, 'codigo' => $cita->codigo]))
            ->assertSessionHas('success');
        $this->assertNotNull($cita->fresh()->validada_at);

        // Una segunda lectura avisa de que ya se validó
        $this->actingAs($admin)->post(route('admin.citas.validar.store', ['cita' => $cita, 'codigo' => $cita->codigo]))
            ->assertSessionHas('error');

        // Ya realizada: no se puede cancelar ni cambiar
        $this->assertFalse($cita->fresh()->sePuedeCancelar());
        $this->assertFalse($cita->fresh()->tieneQr());
    }

    public function test_un_codigo_falso_no_sirve(): void
    {
        $cita = $this->citaConfirmada();
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get(route('admin.citas.validar', ['cita' => $cita, 'codigo' => 'inventado']))->assertNotFound();
        $this->actingAs($admin)->post(route('admin.citas.validar.store', ['cita' => $cita, 'codigo' => 'inventado']))->assertNotFound();
        $this->assertNull($cita->fresh()->validada_at);
    }

    public function test_una_cita_cancelada_no_se_valida(): void
    {
        $cita = $this->citaConfirmada();
        $cita->update(['estado' => 'cancelada']);
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->post(route('admin.citas.validar.store', ['cita' => $cita, 'codigo' => $cita->codigo]))->assertSessionHas('error');
        $this->assertNull($cita->fresh()->validada_at);
    }

    public function test_solo_el_admin_usa_el_lector(): void
    {
        $cita = $this->citaConfirmada();

        $this->actingAs($cita->user)->get(route('admin.escaner'))->assertForbidden();
        $this->actingAs($cita->user)->get(route('admin.citas.validar', ['cita' => $cita, 'codigo' => $cita->codigo]))->assertForbidden();

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get(route('admin.escaner'))->assertOk()->assertSee('html5-qrcode');
        $this->actingAs($admin)->get('/')->assertSee(route('admin.escaner'));
        $this->actingAs($cita->user)->get('/')->assertDontSee(route('admin.escaner'));
    }

    public function test_la_pagina_de_niveles_es_publica(): void
    {
        $this->get(route('niveles'))->assertOk()->assertSee('Bronze')->assertSee('Silver')->assertSee('Gold')->assertSee('−20 %');
    }

    public function test_el_letrero_invita_al_siguiente_nivel(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('citas'))
            ->assertSee('Consigue un 10 % de descuento con tu nivel Bronze')
            ->assertSee(route('niveles'))
            ->assertDontSee('Te faltan');
    }
}
