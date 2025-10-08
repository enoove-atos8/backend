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
        if (!Schema::hasColumn('entries_report_requests', 'account_id'))
        {
            Schema::table('entries_report_requests', function (Blueprint $table)
            {
                $table->unsignedBigInteger('account_id')->nullable()->after('id');

                $table->foreign('account_id')
                    ->references('id')
                    ->on('accounts');
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
