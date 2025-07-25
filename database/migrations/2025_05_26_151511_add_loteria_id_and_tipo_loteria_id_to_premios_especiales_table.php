<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('premios_especiales', function (Blueprint $table) {
            $table->unsignedBigInteger('loteria_id')->nullable()->after('rifa_id');
            $table->unsignedBigInteger('tipo_loteria_id')->nullable()->after('loteria_id');
        });
    }

    public function down()
    {
        Schema::table('premios_especiales', function (Blueprint $table) {
            $table->dropColumn('loteria_id');
            $table->dropColumn('tipo_loteria_id');
        });
    }
};
