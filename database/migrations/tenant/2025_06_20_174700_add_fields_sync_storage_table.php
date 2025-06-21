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
        if (!Schema::hasColumn('sync_storage', 'invoice_id'))
        {
            Schema::table('sync_storage', function (Blueprint $table)
            {
                $table->unsignedBigInteger('invoice_id')->nullable()->after('payment_category_id');

                $table->foreign('invoice_id')
                    ->references('id')
                    ->on('cards_invoices');
            });
        }

        if (!Schema::hasColumn('sync_storage', 'credit_card_payment'))
        {
            Schema::table('sync_storage', function (Blueprint $table)
            {
                $table->boolean('credit_card_payment')->nullable()->after('invoice_id');
            });
        }


        if (!Schema::hasColumn('sync_storage', 'establishment_name'))
        {
            Schema::table('sync_storage', function (Blueprint $table)
            {
                $table->string('establishment_name')->nullable()->after('invoice_id');
            });
        }


        if (!Schema::hasColumn('sync_storage', 'purchase_description'))
        {
            Schema::table('sync_storage', function (Blueprint $table)
            {
                $table->string('purchase_description')->nullable()->after('invoice_id');
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
