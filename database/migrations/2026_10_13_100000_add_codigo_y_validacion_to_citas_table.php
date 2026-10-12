<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            // Código secreto que va dentro del QR (así no se puede validar una cita adivinando su número)
            $table->string('codigo', 40)->nullable()->unique();
            // Cuándo la masajista escaneó el QR y dio la sesión por realizada
            $table->dateTime('validada_at')->nullable();
        });

        // Las citas que ya existían también reciben su código
        foreach (DB::table('citas')->whereNull('codigo')->pluck('id') as $id) {
            DB::table('citas')->where('id', $id)->update(['codigo' => Str::random(32)]);
        }
    }

    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropUnique(['codigo']);
            $table->dropColumn(['codigo', 'validada_at']);
        });
    }
};
