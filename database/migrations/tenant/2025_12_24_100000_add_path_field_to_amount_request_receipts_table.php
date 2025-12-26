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
        if (Schema::hasTable('amount_request_receipts')) {
            Schema::table('amount_request_receipts', function (Blueprint $table) {
                $table->string('path')->nullable()->after('description')->comment('Caminho base para armazenamento do arquivo');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('amount_request_receipts')) {
            Schema::table('amount_request_receipts', function (Blueprint $table) {
                $table->dropColumn('path');
            });
        }
    }
};
