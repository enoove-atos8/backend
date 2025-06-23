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
        if (!Schema::hasColumn('cards_purchases', 'establishment_name'))
        {
            Schema::table('cards_purchases', function (Blueprint $table)
            {
                $table->string('establishment_name')->nullable(false)->after('installment_amount');
            });
        }

        if (!Schema::hasColumn('cards_purchases', 'purchase_description'))
        {
            Schema::table('cards_purchases', function (Blueprint $table)
            {
                $table->string('purchase_description')->nullable(false)->after('establishment_name');
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
        Schema::dropIfExists('cards_purchases');
    }
};
