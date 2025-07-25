<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('abonos', function (Blueprint $table) {
            // Evitar duplicados al volver a ejecutar
            if (!Schema::hasColumn('abonos', 'metodo_pago')) {
                $table->enum('metodo_pago', [
                    'reserva',
                    'efectivo',
                    'transferencia_nacional',
                    'transferencia_internacional',
                    'pago_movil',
                    'zelle',
                ])
                ->after('monto')
                ->default('reserva');
            }

            if (!Schema::hasColumn('abonos', 'titular')) {
                $table->string('titular')
                      ->nullable()
                      ->after('cedula');
            }
        });
    }

    public function down()
    {
        Schema::table('abonos', function (Blueprint $table) {
            if (Schema::hasColumn('abonos', 'metodo_pago')) {
                $table->dropColumn('metodo_pago');
            }
            if (Schema::hasColumn('abonos', 'titular')) {
                $table->dropColumn('titular');
            }
        });
    }
};
