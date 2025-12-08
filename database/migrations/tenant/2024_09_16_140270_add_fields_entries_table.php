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
        if (!Schema::hasColumn('entries', 'group_received_id'))
        {
            Schema::table('entries', function (Blueprint $table)
            {
                $table->integer('group_received_id')->nullable()->after('cult_financial_data_id')->comment('FK ecclesiastical_divisions_groups.id - grupo que recebeu');

                $table->foreign('group_received_id')
                    ->references('id')
                    ->on('ecclesiastical_divisions_groups');
            });
        }

        if (!Schema::hasColumn('entries', 'group_returned_id'))
        {
            Schema::table('entries', function (Blueprint $table)
            {
                $table->integer('group_returned_id')->nullable()->after('cult_financial_data_id')->comment('FK ecclesiastical_divisions_groups.id - grupo que devolveu');

                $table->foreign('group_returned_id')
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
        Schema::dropIfExists('entries');
    }
};
