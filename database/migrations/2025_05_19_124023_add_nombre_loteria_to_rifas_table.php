<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rifas', function (Blueprint $table) {
            // Agrega nombre_loteria justo después de la PK id
            $table->string('nombre_loteria', 100)->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('rifas', function (Blueprint $table) {
            $table->dropColumn('nombre_loteria');
        });
    }
};
