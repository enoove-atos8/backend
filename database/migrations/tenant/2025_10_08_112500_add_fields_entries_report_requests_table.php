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
        if (!Schema::hasColumn('entries_report_requests', 'monthly_entries_amount'))
        {
            Schema::table('entries_report_requests', function (Blueprint $table)
            {
                $table->decimal('monthly_entries_amount')->nullable()->after('tithe_amount');
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
