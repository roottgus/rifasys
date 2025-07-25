<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('rifas', function (Blueprint $table) {
            // Ajusta el after() al lugar que quieras
            $table->string('nombre_loteria')->after('id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('rifas', function (Blueprint $table) {
            $table->dropColumn('nombre_loteria');
        });
    }
};
