<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestamos', function (Blueprint $table) {
            $table->id('id_prestamo');

            $table->foreignId('usuario_id')
                  ->constrained('usuarios')
                  ->onDelete('restrict');

            $table->foreignId('ejemplar_id')
                  ->constrained('ejemplares')
                  ->onDelete('restrict');

            $table->date('fecha_prestamo');
            $table->date('fecha_devolucion_esperada')->nullable();
            $table->date('fecha_devolucion_real')->nullable();
            $table->enum('estado_prestamo', ['activo', 'devuelto', 'vencido', 'renovado'])
                  ->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestamos');
    }
};