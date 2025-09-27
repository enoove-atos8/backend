<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        if (!Schema::hasColumn('entries_report_requests', 'include_cash_deposit'))
        {
            Schema::table('entries_report_requests', function (Blueprint $table)
            {
                $table->boolean('include_cash_deposit')->default(0)->nullable()->after('all_groups_receipts');
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
        Schema::dropIfExists('entries_report_requests');
    }
};
