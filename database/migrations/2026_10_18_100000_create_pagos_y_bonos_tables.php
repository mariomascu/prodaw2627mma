<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Packs de yoga comprados: se paga el pack completo y cada clase realizada resta una sesión
        Schema::create('bonos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('servicio_id')->nullable()->constrained('servicios')->nullOnDelete();
            $table->unsignedSmallInteger('sesiones_totales');
            $table->unsignedSmallInteger('sesiones_usadas')->default(0);
            $table->string('estado', 20)->default('pendiente'); // pendiente (de pago), activo, cancelado
            $table->timestamps();
        });

        // Cada cobro con Stripe (una cita de masaje, un curso aceptado o un bono de yoga)
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cita_id')->nullable()->constrained('citas')->nullOnDelete();
            $table->foreignId('bono_id')->nullable()->constrained('bonos')->nullOnDelete();
            $table->string('concepto');
            $table->unsignedInteger('importe');             // en céntimos de euro
            $table->string('estado', 20)->default('pendiente'); // pendiente, pagado, reembolsado, cancelado
            $table->string('stripe_session_id')->nullable()->unique();
            $table->string('stripe_payment_intent')->nullable();
            $table->dateTime('pagado_at')->nullable();
            $table->dateTime('reembolsado_at')->nullable();
            $table->timestamps();
        });

        Schema::table('citas', function (Blueprint $table) {
            $table->foreignId('bono_id')->nullable()->constrained('bonos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bono_id');
        });
        Schema::dropIfExists('pagos');
        Schema::dropIfExists('bonos');
    }
};
