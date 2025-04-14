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
        if (!Schema::hasColumn('sync_storage', 'payment_category_id'))
        {
            Schema::table('sync_storage', function (Blueprint $table)
            {
                $table->foreign('payment_category_id')
                    ->references('id')
                    ->on('payment_category');
            });
        }

        if (!Schema::hasColumn('sync_storage', 'payment_item_id'))
        {
            Schema::table('sync_storage', function (Blueprint $table)
            {
                $table->foreign('payment_item_id')
                    ->references('id')
                    ->on('payment_item');
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
