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
    Schema::table('abonos', function (Illuminate\Database\Schema\Blueprint $table) {
        $table->string('cuenta_admin_destino')->nullable()->after('banco');
    });
}

public function down()
{
    Schema::table('abonos', function (Illuminate\Database\Schema\Blueprint $table) {
        $table->dropColumn('cuenta_admin_destino');
    });
}

};
