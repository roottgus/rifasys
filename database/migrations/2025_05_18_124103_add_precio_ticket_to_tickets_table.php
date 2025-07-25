<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrecioTicketToTicketsTable extends Migration
{
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Ajusta la precisión/escala según tus necesidades
            $table->decimal('precio_ticket', 10, 2)
                  ->after('numero')
                  ->default(0);
        });
    }

    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('precio_ticket');
        });
    }
}
