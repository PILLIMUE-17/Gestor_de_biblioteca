<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donantes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_donante', 100);
            $table->enum('tipo_donante', ['persona', 'empresa', 'institución']);
            $table->string('email_donante', 100)->nullable();
            $table->string('telefono_donante', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donantes');
    }
};