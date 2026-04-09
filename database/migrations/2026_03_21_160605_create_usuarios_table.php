<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tipo_identificacion_id')
                  ->constrained('tipos_identificacion')
                  ->onDelete('restrict');

            $table->string('nombre_usuario', 100);
            $table->string('nombre2_usuario', 100)->nullable();
            $table->string('apellido_usuario', 100);
            $table->string('apellido2_usuario', 100)->nullable();
            $table->string('numero_identificacion_usuario', 50)->unique();
            $table->string('email_usuario', 100)->unique();
            $table->string('telefono_usuario', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};