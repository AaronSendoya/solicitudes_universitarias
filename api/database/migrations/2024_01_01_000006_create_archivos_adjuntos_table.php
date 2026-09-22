<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archivos_adjuntos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_id')->constrained('solicitudes')->cascadeOnDelete();
            $table->foreignId('usuario_subidor_id')->constrained('usuarios');
            $table->string('nombre_archivo');
            $table->string('url_archivo');
            $table->string('tipo_archivo')->nullable();
            $table->unsignedBigInteger('tamano_bytes')->nullable();
            $table->timestamp('fecha_subida')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archivos_adjuntos');
    }
};
