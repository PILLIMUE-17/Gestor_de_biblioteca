<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_identificacion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_identificacion', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_identificacion');
    }
};
