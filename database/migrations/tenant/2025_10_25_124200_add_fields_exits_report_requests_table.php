<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasColumn('exits_reports_request', 'amount'))
        {
            Schema::table('exits_reports_request', function (Blueprint $table)
            {
                $table->decimal('amount', 10, 2)->nullable(false)->default(0)->after('link_report');
            });
        }
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('exits_reports_request', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
    }
};
