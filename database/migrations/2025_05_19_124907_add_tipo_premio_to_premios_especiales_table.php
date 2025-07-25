<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('premios_especiales', function (Blueprint $table) {
            // Enum con las opciones: dinero, articulo, moto, otro
            $table->enum('tipo_premio', ['dinero', 'articulo', 'moto', 'otro'])
                  ->default('dinero')
                  ->after('tipo_loteria');
        });
    }

    public function down(): void
    {
        Schema::table('premios_especiales', function (Blueprint $table) {
            $table->dropColumn('tipo_premio');
        });
    }
};
