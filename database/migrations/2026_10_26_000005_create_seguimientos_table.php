<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seguimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incidencia_id')->constrained('incidencias')->cascadeOnDelete();
            $table->foreignId('tecnico_id')->nullable()->constrained('tecnicos')->nullOnDelete();
            $table->string('estado');
            $table->text('comentario');
            $table->timestamps();
            $table->index(['incidencia_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seguimientos');
    }
};
