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
        if (!Schema::hasColumn('entries_report_requests', 'tithe_amount'))
        {
            Schema::table('entries_report_requests', function (Blueprint $table)
            {
                $table->decimal('tithe_amount')->nullable(false)->default(0)->after('link_report');
            });
        }

        if (!Schema::hasColumn('entries_report_requests', 'designated_amount'))
        {
            Schema::table('entries_report_requests', function (Blueprint $table)
            {
                $table->decimal('designated_amount')->nullable(false)->default(0)->after('link_report');
            });
        }

        if (!Schema::hasColumn('entries_report_requests', 'offer_amount'))
        {
            Schema::table('entries_report_requests', function (Blueprint $table)
            {
                $table->decimal('offer_amount')->nullable(false)->default(0)->after('link_report');
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
