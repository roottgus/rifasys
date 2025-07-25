<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1) Eliminar duplicados, conservando el registro con menor ID
        DB::statement(<<<'SQL'
            DELETE t1
            FROM tickets t1
            INNER JOIN tickets t2
              ON t1.rifa_id = t2.rifa_id
             AND t1.numero  = t2.numero
             AND t1.id     > t2.id
        SQL);

        // 2) Crear el índice único compuesto
        Schema::table('tickets', function (Blueprint $table) {
            $table->unique(
                ['rifa_id', 'numero'],
                'tickets_rifa_numero_unique'
            );
        });
    }

    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropUnique('tickets_rifa_numero_unique');
        });
    }
};
