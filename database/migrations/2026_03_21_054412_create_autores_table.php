<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('autores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_autor', 100);
            $table->string('nombre2_autor', 100)->nullable();
            $table->string('apellido_autor', 100);
            $table->string('apellido2_autor', 100)->nullable();
            $table->string('nacionalidad_autor', 80)->nullable();
            $table->date('fecha_nacimiento_autor')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('autores');
    }
};