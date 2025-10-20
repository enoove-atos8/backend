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
        if (!Schema::hasColumn('sync_storage', 'origin_account_id'))
        {
            Schema::table('sync_storage', function (Blueprint $table)
            {
                $table->unsignedBigInteger('origin_account_id')->nullable()->after('account_id');

                $table->foreign('origin_account_id')
                    ->references('id')
                    ->on('accounts');
            });
        }

        if (!Schema::hasColumn('sync_storage', 'destination_account_id'))
        {
            Schema::table('sync_storage', function (Blueprint $table)
            {
                $table->unsignedBigInteger('destination_account_id')->nullable()->after('origin_account_id');

                $table->foreign('destination_account_id')
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
        Schema::dropIfExists('sync_storage');
    }
};
