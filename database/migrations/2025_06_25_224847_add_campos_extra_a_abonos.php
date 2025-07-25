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
    Schema::table('abonos', function (Blueprint $table) {
        $table->string('correo')->nullable()->after('banco'); // para Zelle
        $table->string('lugar_pago')->nullable()->after('referencia'); // para efectivo
        $table->text('nota')->nullable()->after('lugar_pago'); // nota interna
    });
}


    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('abonos', function (Blueprint $table) {
        $table->dropColumn(['correo', 'lugar_pago', 'nota']);
    });
}

};
