<?php
// database/migrations/2025_05_17_000000_add_tipo_y_detalles_abonos_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoYDetallesToAbonosTable extends Migration
{
    public function up()
    {
        Schema::table('abonos', function (Blueprint $table) {
            $table->string('tipo')->after('ticket_id')->default('efectivo');
            // Campos específicos para Pago Móvil y otros:
            $table->string('telefono')->nullable();
            $table->string('cedula')->nullable();
            $table->string('banco')->nullable();
            $table->string('referencia')->nullable();
        });
    }

    public function down()
    {
        Schema::table('abonos', function (Blueprint $table) {
            $table->dropColumn(['tipo','telefono','cedula','banco','referencia']);
        });
    }
}