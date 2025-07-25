<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('premios_especiales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rifa_id')
                  ->constrained('rifas')
                  ->cascadeOnDelete();
            $table->string('nombre_loteria')->default('Lotería del Táchira');
            $table->string('tipo_loteria');
            $table->date('fecha_premio');
            $table->time('hora_premio');
            $table->string('descripcion');
            $table->decimal('monto', 10, 2);
            $table->decimal('abono_minimo', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('premios_especiales');
    }
};
