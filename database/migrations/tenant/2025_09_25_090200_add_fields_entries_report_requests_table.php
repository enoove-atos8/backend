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
        if (!Schema::hasColumn('entries_report_requests', 'include_groups_entries'))
        {
            Schema::table('entries_report_requests', function (Blueprint $table)
            {
                $table->boolean('include_groups_entries')->default(false)->nullable()->after('all_groups_receipts');
            });
        }

        if (!Schema::hasColumn('entries_report_requests', 'include_anonymous_offers'))
        {
            Schema::table('entries_report_requests', function (Blueprint $table)
            {
                $table->boolean('include_anonymous_offers')->default(false)->nullable()->after('include_groups_entries');
            });
        }

        if (!Schema::hasColumn('entries_report_requests', 'include_transfers_between_accounts'))
        {
            Schema::table('entries_report_requests', function (Blueprint $table)
            {
                $table->boolean('include_transfers_between_accounts')->default(false)->nullable()->after('include_anonymous_offers');
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
