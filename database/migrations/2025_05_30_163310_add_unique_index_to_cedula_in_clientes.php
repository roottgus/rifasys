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
    \Illuminate\Support\Facades\Schema::table('clientes', function ($table) {
        $table->unique('cedula');
    });
}

public function down()
{
    \Illuminate\Support\Facades\Schema::table('clientes', function ($table) {
        $table->dropUnique(['cedula']);
    });
}

};
