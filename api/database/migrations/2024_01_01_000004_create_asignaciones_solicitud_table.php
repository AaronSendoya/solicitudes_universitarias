<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignaciones_solicitud', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_id')->constrained('solicitudes')->cascadeOnDelete();
            $table->foreignId('usuario_asignado_id')->constrained('usuarios');
            $table->foreignId('asignado_por_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamp('fecha_asignacion')->nullable();
            $table->text('comentarios_asignacion')->nullable();
            $table->string('estado_asignacion')->default('Activa');
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignaciones_solicitud');
    }
};
