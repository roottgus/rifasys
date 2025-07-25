<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('tickets', function (Blueprint $table) {
        $table->id();
        $table->foreignId('rifa_id')->constrained()->cascadeOnDelete();
        $table->foreignId('cliente_id')->nullable()->constrained()->nullOnDelete();
        $table->string('uuid')->unique();
        $table->integer('numero')->nullable();
        $table->enum('estado', ['disponible','vendido','verificado'])->default('disponible');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
