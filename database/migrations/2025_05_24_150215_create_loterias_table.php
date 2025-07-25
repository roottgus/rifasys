<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up()
    {
        // Solo crea la tabla si no existe ya
        if (! Schema::hasTable('loterias')) {
            Schema::create('loterias', function (Blueprint $table) {
                $table->id();
                $table->string('nombre')->unique();
                $table->text('descripcion')->nullable();
                $table->date('fecha_sorteo');
                $table->time('hora_sorteo');
                $table->decimal('precio_ticket', 10, 2);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('loterias');
    }
};
