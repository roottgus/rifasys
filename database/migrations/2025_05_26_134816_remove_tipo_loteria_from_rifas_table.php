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
        $table->dropColumn('tipo_loteria');
    });
}
public function down()
{
    Schema::table('rifas', function (Blueprint $table) {
        $table->string('tipo_loteria')->nullable();
    });
}

};
