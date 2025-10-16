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
        if (!Schema::hasColumn('accounts_movements', 'file_id'))
        {
            Schema::table('accounts_movements', function (Blueprint $table)
            {
                $table->unsignedBigInteger('file_id')->nullable()->after('account_id');
                $table->foreign('file_id')->references('id')->on('accounts_files')->onDelete('set null');
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
        if (Schema::hasColumn('accounts_movements', 'file_id'))
        {
            Schema::table('accounts_movements', function (Blueprint $table)
            {
                $table->dropForeign(['file_id']);
                $table->dropColumn('file_id');
            });
        }
    }
};
