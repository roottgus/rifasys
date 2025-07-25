<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tipos_loteria', function (Blueprint $table) {
            // Borra el índice único actual solo en 'nombre'
            $table->dropUnique('tipos_loteria_nombre_unique');
            // Crea un índice único compuesto sobre nombre+loteria_id
            $table->unique(['nombre', 'loteria_id'], 'tipos_loteria_nombre_loteria_id_unique');
        });
    }

    public function down()
    {
        Schema::table('tipos_loteria', function (Blueprint $table) {
            // Borra el índice único compuesto
            $table->dropUnique('tipos_loteria_nombre_loteria_id_unique');
            // Crea el índice único solo en nombre (como estaba antes)
            $table->unique('nombre', 'tipos_loteria_nombre_unique');
        });
    }
};
