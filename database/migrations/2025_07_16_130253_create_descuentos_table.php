<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDescuentosTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('descuentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rifa_id');
            $table->integer('cantidad_minima');
            $table->decimal('porcentaje', 5, 2); // Ej: 20.00 = 20%
            $table->timestamps();

            $table->foreign('rifa_id')->references('id')->on('rifas')->onDelete('cascade');
            $table->index(['rifa_id', 'cantidad_minima']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('descuentos');
    }
}
