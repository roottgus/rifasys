<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Si ya todos los registros tienen loteria_id (como ya los actualizaste antes)
        Schema::table('tipos_loteria', function (Blueprint $table) {
            $table->unsignedBigInteger('loteria_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('tipos_loteria', function (Blueprint $table) {
            $table->unsignedBigInteger('loteria_id')->nullable()->change();
        });
    }
};

