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
        if (!Schema::hasColumn('sync_storage', 'card_id'))
        {
            Schema::table('sync_storage', function (Blueprint $table)
            {
                $table->unsignedBigInteger('card_id')->nullable()->after('payment_item_id');

                $table->foreign('card_id')
                    ->references('id')
                    ->on('cards');
            });
        }

        if (!Schema::hasColumn('sync_storage', 'invoice_closed_day'))
        {
            Schema::table('sync_storage', function (Blueprint $table)
            {
                $table->string('invoice_closed_day')->nullable()->after('card_id');
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
