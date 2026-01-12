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
        DB::statement("ALTER TABLE ecclesiastical_divisions_groups ADD COLUMN ministerial_investment_limit DECIMAL(10,2) NULL DEFAULT NULL COMMENT 'Limite disponível para Investimento Ministerial (NULL = sem permissão)' AFTER return_receiving");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE ecclesiastical_divisions_groups DROP COLUMN ministerial_investment_limit');
    }
};
