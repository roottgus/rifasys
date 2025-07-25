<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetalleArticuloToPremiosEspecialesTable extends Migration
{
    public function up()
    {
        Schema::table('premios_especiales', function (Blueprint $table) {
            // Añadimos la columna justo después de tipo_premio
            $table->string('detalle_articulo')->nullable()->after('tipo_premio');
        });
    }

    public function down()
    {
        Schema::table('premios_especiales', function (Blueprint $table) {
            // Para revertir
            $table->dropColumn('detalle_articulo');
        });
    }
}
