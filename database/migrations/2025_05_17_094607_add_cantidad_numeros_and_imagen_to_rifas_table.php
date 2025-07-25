<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rifas', function (Blueprint $table) {
            // Cantidad total de números que tendrá la rifa
            $table->integer('cantidad_numeros')
                  ->after('precio')
                  ->default(100); // valor por defecto, ajústalo si quieres
            // Ruta del archivo de imagen
            $table->string('imagen')
                  ->after('descripcion')
                  ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('rifas', function (Blueprint $table) {
            $table->dropColumn(['cantidad_numeros', 'imagen']);
        });
    }
};
