<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estados_solicitud', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->timestamps();
        });

        Schema::create('tipos_solicitud', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->timestamps();
        });

        Schema::create('prioridades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->unsignedTinyInteger('nivel')->default(1);
            $table->timestamps();
        });

        Schema::create('ubicaciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->timestamps();
        });

        Schema::create('recursos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('tipo')->nullable();
            $table->string('estado')->default('Disponible');
            $table->foreignId('ubicacion_id')->nullable()->constrained('ubicaciones')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recursos');
        Schema::dropIfExists('ubicaciones');
        Schema::dropIfExists('prioridades');
        Schema::dropIfExists('tipos_solicitud');
        Schema::dropIfExists('estados_solicitud');
    }
};
