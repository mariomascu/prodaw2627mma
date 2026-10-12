<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
            $table->boolean('is_admin')->default(false)->after('password');
            $table->string('telefono', 20)->nullable()->after('email');
            // Cuestionario del registro: sirve para catalogar a los clientes
            $table->string('sexo')->nullable();
            $table->string('objetivo')->nullable();
            $table->string('frecuencia')->nullable();
            $table->timestamp('perfil_completado_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn(['username', 'is_admin', 'telefono', 'sexo', 'objetivo', 'frecuencia', 'perfil_completado_at']);
        });
    }
};
