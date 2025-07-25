<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentMethodsTable extends Migration
{
    public function up()
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();     // ej. 'zelle'
            $table->string('name');              // ej. 'Zelle'
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });

        // Seed inicial
        DB::table('payment_methods')->insert([
            ['key'=>'tran_bancaria_nacional',      'name'=>'Transferencia Bancaria Nacional',      'enabled'=>true],
            ['key'=>'pago_efectivo',               'name'=>'Pago Efectivo',                          'enabled'=>true],
            ['key'=>'pago_movil',                  'name'=>'Pago Móvil',                             'enabled'=>true],
            ['key'=>'tran_bancaria_internacional', 'name'=>'Transferencia Bancaria Internacional', 'enabled'=>true],
            ['key'=>'zelle',                       'name'=>'Zelle',                                  'enabled'=>true],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('payment_methods');
    }
}
