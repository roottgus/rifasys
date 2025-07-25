<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropReferenceNumberFromAbonos extends Migration
{
    public function up()
    {
        Schema::table('abonos', function (Blueprint $table) {
            // Sólo si existe la columna, para evitar errores
            if (Schema::hasColumn('abonos', 'reference_number')) {
                $table->dropColumn('reference_number');
            }
        });
    }

    public function down()
    {
        Schema::table('abonos', function (Blueprint $table) {
            // La volvemos a crear como nullable para poder revertir
            $table->string('reference_number')->nullable();
        });
    }
}
