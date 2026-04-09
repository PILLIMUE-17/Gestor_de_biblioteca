<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
         Schema::create('libros', function (Blueprint $table) {
        // Campos de la tabla libros      
         $table->id();
        $table->unsignedBigInteger('autor_id');
        $table->unsignedBigInteger('editorial_id');
        $table->unsignedBigInteger('subcategoria_id');
        $table->string('titulo_libro', 255);
        $table->string('isbn_libro', 20)->unique()->nullable();
        $table->year('anio_publicacion_libro')->nullable();
        $table->text('descripcion_libro')->nullable();
        $table->timestamps();
        // Claves foráneas
        $table->foreign('autor_id')
              ->references('id')->on('autores')
              ->onDelete('restrict');
        $table->foreign('editorial_id')
              ->references('id')->on('editoriales')
              ->onDelete('restrict');
        $table->foreign('subcategoria_id')
              ->references('id')->on('subcategorias')
              ->onDelete('restrict');
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('libros');
    }
};
