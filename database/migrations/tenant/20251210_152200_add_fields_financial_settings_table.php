<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adiciona coluna budget_type para identificar o tipo de orçamento
     */
    public function up(): void
    {
        Schema::table('financial_settings', function (Blueprint $table) {
            $table->string('budget_type', 20)->nullable()->after('budget_value')->comment('Tipo do orçamento: tithes ou exits');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('financial_settings', function (Blueprint $table) {
            $table->dropColumn('budget_type');
        });
    }
};
