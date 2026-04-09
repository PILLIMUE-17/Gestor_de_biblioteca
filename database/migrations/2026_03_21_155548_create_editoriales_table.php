<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('editoriales', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_editorial', 100);
            $table->string('pais_origen_editorial', 100)->nullable();
            $table->string('telefono_editorial', 20)->nullable();
            $table->string('email_editorial', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('editoriales');
    }
};