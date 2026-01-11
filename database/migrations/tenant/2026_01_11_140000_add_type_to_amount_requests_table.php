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
        DB::statement("ALTER TABLE amount_requests ADD COLUMN type ENUM('group_fund', 'ministerial_investment') DEFAULT 'group_fund' COMMENT 'Tipo: group_fund=Verba de Grupo, ministerial_investment=Investimento Ministerial' AFTER group_id");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE amount_requests DROP COLUMN type');
    }
};
