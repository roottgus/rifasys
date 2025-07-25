<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Si usas doctrine/dbal, podrías usar ->change() directamente.
        // Aquí lo hacemos con raw SQL para MySQL:
        DB::statement("
            ALTER TABLE `tickets`
            MODIFY `estado` ENUM('disponible','apartado','vendido','verificado')
            NOT NULL
            DEFAULT 'disponible'
        ");
    }

    public function down()
    {
        DB::statement("
            ALTER TABLE `tickets`
            MODIFY `estado` ENUM('disponible','vendido','verificado')
            NOT NULL
            DEFAULT 'disponible'
        ");
    }
};
