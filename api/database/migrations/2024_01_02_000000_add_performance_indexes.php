<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->index('estado_id');
            $table->index('usuario_solicitante_id');
            $table->index('usuario_responsable_actual_id');
            $table->index('fecha_creacion');
        });

        Schema::table('comentarios_solicitud', function (Blueprint $table) {
            $table->index('solicitud_id');
        });

        Schema::table('archivos_adjuntos', function (Blueprint $table) {
            $table->index('solicitud_id');
        });

        Schema::table('asignaciones_solicitud', function (Blueprint $table) {
            $table->index('solicitud_id');
            $table->index('usuario_asignado_id');
        });

        Schema::table('historial_solicitud', function (Blueprint $table) {
            $table->index('solicitud_id');
        });
    }

    public function down(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->dropIndex(['estado_id']);
            $table->dropIndex(['usuario_solicitante_id']);
            $table->dropIndex(['usuario_responsable_actual_id']);
            $table->dropIndex(['fecha_creacion']);
        });

        Schema::table('comentarios_solicitud', function (Blueprint $table) {
            $table->dropIndex(['solicitud_id']);
        });

        Schema::table('archivos_adjuntos', function (Blueprint $table) {
            $table->dropIndex(['solicitud_id']);
        });

        Schema::table('asignaciones_solicitud', function (Blueprint $table) {
            $table->dropIndex(['solicitud_id']);
            $table->dropIndex(['usuario_asignado_id']);
        });

        Schema::table('historial_solicitud', function (Blueprint $table) {
            $table->dropIndex(['solicitud_id']);
        });
    }
};
