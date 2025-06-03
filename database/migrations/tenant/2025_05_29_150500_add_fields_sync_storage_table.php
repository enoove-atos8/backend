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
        if (!Schema::hasColumn('sync_storage', 'purchase_credit_card_installment_amount'))
        {
            Schema::table('sync_storage', function (Blueprint $table)
            {
                $table->decimal('purchase_credit_card_installment_amount')->nullable()->after('purchase_credit_card_amount');
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
