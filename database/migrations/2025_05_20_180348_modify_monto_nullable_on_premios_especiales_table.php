<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyMontoNullableOnPremiosEspecialesTable extends Migration
{
    public function up()
    {
        Schema::table('premios_especiales', function (Blueprint $table) {
            // Cambiar monto a nullable
            $table
                ->decimal('monto', 10, 2)
                ->nullable()
                ->change();
        });
    }

    public function down()
    {
        Schema::table('premios_especiales', function (Blueprint $table) {
            // Si deshaces, volver a not nullable
            $table
                ->decimal('monto', 10, 2)
                ->nullable(false)
                ->change();
        });
    }
}
