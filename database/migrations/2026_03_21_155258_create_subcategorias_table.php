<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subcategorias', function (Blueprint $table) {
            $table->id();
           
            // FK hacia categorias — debe existir primero
            $table->foreignId('categoria_id')
                  ->constrained('categorias')
                  ->onDelete('restrict');

            $table->string('nombre_subcategoria', 100);
            $table->string('codigo_subcategoria', 10)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subcategorias');
    }
};




