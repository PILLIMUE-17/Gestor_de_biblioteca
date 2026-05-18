<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE prestamos ADD COLUMN multa DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER estado_prestamo');
        DB::statement('ALTER TABLE prestamos ADD COLUMN multa_pagada TINYINT(1) NOT NULL DEFAULT 1 AFTER multa');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE prestamos DROP COLUMN multa_pagada');
        DB::statement('ALTER TABLE prestamos DROP COLUMN multa');
    }
};
