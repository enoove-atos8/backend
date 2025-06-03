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
        if (!Schema::hasColumn('cards_installments', 'purchase_id'))
        {
            Schema::table('cards_installments', function (Blueprint $table)
            {
                $table->unsignedBigInteger('purchase_id')->nullable()->after('invoice_id');

                $table->foreign('purchase_id')
                    ->references('id')
                    ->on('cards_purchases');
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
        Schema::dropIfExists('cards_installments');
    }
};
