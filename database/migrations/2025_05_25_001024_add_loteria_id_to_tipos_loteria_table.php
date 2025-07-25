<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('tipos_loteria', function (Blueprint $table) {
        // 1) Añadimos la columna como nullable
        $table->foreignId('loteria_id')
              ->nullable()
              ->after('nombre')
              ->constrained('loterias')
              ->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('tipos_loteria', function (Blueprint $table) {
        $table->dropForeign(['loteria_id']);
        $table->dropColumn('loteria_id');
    });
}

};
