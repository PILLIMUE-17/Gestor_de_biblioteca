<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ejemplares', function (Blueprint $table) {
            $table->id();

            $table->foreignId('libro_id')
                  ->constrained('libros')
                  ->onDelete('restrict');

            $table->foreignId('donante_id')
                  ->nullable()
                  ->constrained('donantes')
                  ->onDelete('set null');

            $table->integer('numero_copia_ejemplar')->default(1);
            $table->date('fecha_ingreso_ejemplar')->nullable();
            $table->enum('estado_ejemplar', ['disponible', 'prestado', 'dañado', 'baja'])
                  ->default('disponible');
            $table->string('ubicacion_ejemplar', 100)->nullable();
            $table->string('signatura_topografica', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ejemplares');
    }
};