<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameNombreLoteriaToLoteriaIdOnRifas extends Migration
{
    public function up()
    {
        Schema::table('rifas', function (Blueprint $table) {
            // 1) renombramos la columna
            $table->renameColumn('nombre_loteria', 'loteria_id');
            // 2) cambiamos a unsignedBigInteger y FK
            $table->unsignedBigInteger('loteria_id')->change();
            $table->foreign('loteria_id')
                  ->references('id')
                  ->on('loterias')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('rifas', function (Blueprint $table) {
            // para rollback
            $table->dropForeign(['loteria_id']);
            $table->renameColumn('loteria_id', 'nombre_loteria');
        });
    }
}
