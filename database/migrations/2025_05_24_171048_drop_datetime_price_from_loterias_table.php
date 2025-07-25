<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropDatetimePriceFromLoteriasTable extends Migration
{
    public function up()
    {
        // Sólo intentamos dropear si existen
        if (Schema::hasColumn('loterias', 'fecha_sorteo') ||
            Schema::hasColumn('loterias', 'hora_sorteo') ||
            Schema::hasColumn('loterias', 'precio_ticket')) {

            Schema::table('loterias', function (Blueprint $table) {
                if (Schema::hasColumn('loterias', 'fecha_sorteo')) {
                    $table->dropColumn('fecha_sorteo');
                }
                if (Schema::hasColumn('loterias', 'hora_sorteo')) {
                    $table->dropColumn('hora_sorteo');
                }
                if (Schema::hasColumn('loterias', 'precio_ticket')) {
                    $table->dropColumn('precio_ticket');
                }
            });
        }
    }

    public function down()
    {
        Schema::table('loterias', function (Blueprint $table) {
            // En down volvemos a crear las columnas, si no existen
            if (! Schema::hasColumn('loterias', 'fecha_sorteo')) {
                $table->date('fecha_sorteo');
            }
            if (! Schema::hasColumn('loterias', 'hora_sorteo')) {
                $table->time('hora_sorteo');
            }
            if (! Schema::hasColumn('loterias', 'precio_ticket')) {
                $table->decimal('precio_ticket', 10, 2);
            }
        });
    }
}
