<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentMethodAndReferenceToAbonosTable extends Migration
{
    public function up()
    {
        Schema::table('abonos', function (Blueprint $table) {
            // FK al método de pago
            $table->foreignId('payment_method_id')
                  ->nullable()
                  ->constrained('payment_methods')
                  ->after('id');
            // Número de referencia del pago
            $table->string('reference_number')
                  ->unique()
                  ->nullable()
                  ->after('payment_method_id');
        });
    }

    public function down()
    {
        Schema::table('abonos', function (Blueprint $table) {
            $table->dropForeign(['payment_method_id']);
            $table->dropColumn('payment_method_id');
            $table->dropUnique(['reference_number']);
            $table->dropColumn('reference_number');
        });
    }
}
