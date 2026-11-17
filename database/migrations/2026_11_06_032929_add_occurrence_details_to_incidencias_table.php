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
        Schema::table('incidencias', function (Blueprint $table) {
            $table->date('fecha_ocurrencia')->nullable();
            $table->time('hora_ocurrencia')->nullable();
            $table->string('lugar', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidencias', function (Blueprint $table) {
            $table->dropColumn(['fecha_ocurrencia', 'hora_ocurrencia', 'lugar']);
        });
    }
};
