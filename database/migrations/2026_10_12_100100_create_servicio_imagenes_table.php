<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Las imágenes subidas desde el panel se guardan en PostgreSQL:
        // el sistema de ficheros de Vercel es de solo lectura.
        Schema::create('servicio_imagenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servicio_id')->unique()->constrained('servicios')->cascadeOnDelete();
            $table->string('mime', 50);
            $table->text('datos'); // contenido en base64
            $table->timestamps();
        });

        Schema::table('servicios', function (Blueprint $table) {
            $table->timestamp('imagen_actualizada_at')->nullable()->after('imagen');
        });
    }

    public function down(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->dropColumn('imagen_actualizada_at');
        });
        Schema::dropIfExists('servicio_imagenes');
    }
};
