<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('libro_autor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('libro_id')->constrained('libros')->onDelete('cascade');
            $table->foreignId('autor_id')->constrained('autores')->onDelete('cascade');
            $table->unique(['libro_id', 'autor_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('libro_autor');
    }
};
