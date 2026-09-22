<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion');
            $table->timestamp('fecha_creacion')->nullable();
            $table->timestamp('fecha_actualizacion')->nullable();
            $table->timestamp('fecha_cierre')->nullable();

            $table->foreignId('estado_id')->constrained('estados_solicitud');
            $table->foreignId('tipo_id')->constrained('tipos_solicitud');
            $table->foreignId('prioridad_id')->constrained('prioridades');
            $table->foreignId('ubicacion_id')->constrained('ubicaciones');
            $table->foreignId('recurso_id')->nullable()->constrained('recursos')->nullOnDelete();

            $table->foreignId('usuario_solicitante_id')->constrained('usuarios');
            $table->foreignId('usuario_responsable_actual_id')->nullable()->constrained('usuarios')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
