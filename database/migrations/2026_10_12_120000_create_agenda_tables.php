<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cada hora que el administrador abre en la agenda (p. ej. 2026-11-03 10:00)
        Schema::create('franjas', function (Blueprint $table) {
            $table->id();
            $table->dateTime('inicio')->unique();
            $table->boolean('bloqueada')->default(false); // el admin la puede cerrar (festivo, vacaciones…)
            $table->timestamps();
        });

        // Solicitudes de cambio de cita que hace el cliente y aprueba el administrador
        Schema::create('solicitudes_cambio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cita_id')->constrained('citas')->cascadeOnDelete();
            $table->dateTime('inicio');
            $table->dateTime('fin');
            $table->string('estado', 20)->default('pendiente'); // pendiente, aprobada, rechazada
            $table->timestamps();
        });

        // Qué cita ocupa cada franja. franja_id es único: una hora no se puede reservar dos veces.
        // Si solicitud_cambio_id tiene valor, la franja está apartada para un cambio pendiente.
        Schema::create('ocupaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franja_id')->unique()->constrained('franjas')->cascadeOnDelete();
            $table->foreignId('cita_id')->constrained('citas')->cascadeOnDelete();
            $table->foreignId('solicitud_cambio_id')->nullable()->constrained('solicitudes_cambio')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::table('citas', function (Blueprint $table) {
            $table->dateTime('inicio')->nullable();       // día y hora de la sesión
            $table->dateTime('fin')->nullable();
            $table->unsignedTinyInteger('descuento')->nullable(); // % por nivel de fidelidad
            $table->string('precio_final')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropColumn(['inicio', 'fin', 'descuento', 'precio_final']);
        });
        Schema::dropIfExists('ocupaciones');
        Schema::dropIfExists('solicitudes_cambio');
        Schema::dropIfExists('franjas');
    }
};
