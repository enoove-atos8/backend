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
        if (Schema::hasColumn('entries', 'cult_financial_data_id'))
        {
            Schema::table('entries', function (Blueprint $table){
                $table->foreign('cult_financial_data_id')
                        ->references('id')
                        ->on('cult_financial_data');
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
        Schema::dropIfExists('entries');
    }
};
