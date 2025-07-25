<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1) Amplía el ENUM para incluir 'reservado' manteniendo 'apartado'
        DB::statement("
            ALTER TABLE `tickets`
            MODIFY `estado`
                ENUM('disponible','apartado','reservado','abonado','vendido','verificado')
                NOT NULL
                DEFAULT 'disponible'
        ");

        // 2) Convierte los registros existentes
        DB::statement("
            UPDATE `tickets`
            SET `estado` = 'reservado'
            WHERE `estado` = 'apartado'
        ");

        // 3) Ahora quita 'apartado' del ENUM
        DB::statement("
            ALTER TABLE `tickets`
            MODIFY `estado`
                ENUM('disponible','reservado','abonado','vendido','verificado')
                NOT NULL
                DEFAULT 'disponible'
        ");
    }

    public function down()
    {
        // 1) Amplía para volver a incluir 'apartado'
        DB::statement("
            ALTER TABLE `tickets`
            MODIFY `estado`
                ENUM('disponible','reservado','apartado','abonado','vendido','verificado')
                NOT NULL
                DEFAULT 'disponible'
        ");

        // 2) Devuelve los 'reservado' a 'apartado'
        DB::statement("
            UPDATE `tickets`
            SET `estado` = 'apartado'
            WHERE `estado` = 'reservado'
        ");

        // 3) Retira 'reservado' de nuevo
        DB::statement("
            ALTER TABLE `tickets`
            MODIFY `estado`
                ENUM('disponible','apartado','abonado','vendido','verificado')
                NOT NULL
                DEFAULT 'disponible'
        ");
    }
};
