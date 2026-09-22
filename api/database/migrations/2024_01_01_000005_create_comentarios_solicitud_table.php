<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comentarios_solicitud', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_id')->constrained('solicitudes')->cascadeOnDelete();
            $table->foreignId('usuario_autor_id')->constrained('usuarios');
            $table->text('texto_comentario');
            $table->timestamp('fecha_comentario')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comentarios_solicitud');
    }
};
