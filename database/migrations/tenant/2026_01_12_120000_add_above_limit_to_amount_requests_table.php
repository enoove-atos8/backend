<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE amount_requests ADD COLUMN above_limit TINYINT(1) DEFAULT 0 COMMENT 'Indica se a solicitação foi feita acima do limite configurado' AFTER type");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE amount_requests DROP COLUMN above_limit');
    }
};
