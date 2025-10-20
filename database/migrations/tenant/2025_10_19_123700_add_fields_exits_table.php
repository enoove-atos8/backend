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
        if (!Schema::hasColumn('exits', 'account_id'))
        {
            Schema::table('exits', function (Blueprint $table)
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
        Schema::dropIfExists('exits');
    }
};
