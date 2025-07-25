<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('rifas', function (Blueprint $table) {
        $table->unsignedBigInteger('tipo_loteria_id')->nullable()->after('loteria_id');
        $table->foreign('tipo_loteria_id')->references('id')->on('tipos_loteria');
    });
}

public function down()
{
    Schema::table('rifas', function (Blueprint $table) {
        $table->dropForeign(['tipo_loteria_id']);
        $table->dropColumn('tipo_loteria_id');
    });
}

};
