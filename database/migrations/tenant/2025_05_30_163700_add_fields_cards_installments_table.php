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
        if (!Schema::hasColumn('cards_installments', 'invoice_id'))
        {
            Schema::table('cards_installments', function (Blueprint $table)
            {
                $table->unsignedBigInteger('invoice_id')->nullable()->after('card_id');

                $table->foreign('invoice_id')
                    ->references('id')
                    ->on('cards_invoices');
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
