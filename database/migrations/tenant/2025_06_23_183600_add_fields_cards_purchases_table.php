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
        if (!Schema::hasColumn('cards_purchases', 'group_id'))
        {
            Schema::table('cards_purchases', function (Blueprint $table)
            {
                $table->unsignedBigInteger('group_id')->nullable()->after('card_id');

                $table->foreign('group_id')
                    ->references('id')
                    ->on('ecclesiastical_divisions_groups');
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
