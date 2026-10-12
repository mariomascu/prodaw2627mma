<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Historial de versiones de la aplicación (pestaña "Versiones" del panel)
        Schema::create('versiones', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 20)->unique();
            $table->date('fecha');
            $table->text('cambios'); // un cambio por línea
            $table->timestamps();
        });

        // Ajustes del panel de configuración, guardados como clave => valor
        Schema::create('ajustes', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->text('valor')->nullable();
            $table->timestamps();
        });

        // Idioma elegido por cada usuario
        Schema::table('users', function (Blueprint $table) {
            $table->string('idioma', 5)->nullable();
        });

        // Traducciones del contenido de cada servicio (el español está en la tabla servicios)
        Schema::create('servicio_traducciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servicio_id')->constrained('servicios')->cascadeOnDelete();
            $table->string('idioma', 5);
            $table->string('titulo')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('duracion')->nullable();
            $table->string('precio')->nullable();
            $table->timestamps();
            $table->unique(['servicio_id', 'idioma']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicio_traducciones');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('idioma');
        });
        Schema::dropIfExists('ajustes');
        Schema::dropIfExists('versiones');
    }
};
