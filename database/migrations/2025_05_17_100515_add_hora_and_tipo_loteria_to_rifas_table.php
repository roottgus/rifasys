<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rifas', function (Blueprint $table) {
            // Hora de sorteo
            $table->time('hora_sorteo')
                  ->after('fecha_sorteo')
                  ->nullable();
            // Tipo de lotería (puede usarse enum si lo prefieres)
            $table->string('tipo_loteria')
                  ->after('hora_sorteo')
                  ->default('triple_zodiacal');
        });
    }

    public function down(): void
    {
        Schema::table('rifas', function (Blueprint $table) {
            $table->dropColumn(['hora_sorteo', 'tipo_loteria']);
        });
    }
};
