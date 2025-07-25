<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('premios_especiales', function (Blueprint $table) {
            $table->dropColumn('nombre_loteria');
            $table->dropColumn('tipo_loteria');
        });
    }

    public function down()
    {
        Schema::table('premios_especiales', function (Blueprint $table) {
            $table->string('nombre_loteria')->nullable();
            $table->string('tipo_loteria')->nullable();
        });
    }
};
