<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1) Reemplazamos primero el valor antiguo
        DB::table('rifas')
            ->where('tipo_loteria', 'triple_a_b')
            ->update(['tipo_loteria' => 'Triple A']);

        // 2) Ahora convertimos a ENUM
        Schema::table('rifas', function (Blueprint $table) {
            $table->enum('tipo_loteria', [
                'Triple A',
                'Triple B',
                'Triple Zodiacal',
            ])->default('Triple A')->change();
        });
    }

    public function down()
    {
        // 1) Volvemos la columna a string
        Schema::table('rifas', function (Blueprint $table) {
            $table->string('tipo_loteria')->change();
        });

        // 2) (Opcional) revertimos los datos al slug antiguo
        DB::table('rifas')
            ->where('tipo_loteria', 'Triple A')
            ->update(['tipo_loteria' => 'triple_a_b']);
    }
};