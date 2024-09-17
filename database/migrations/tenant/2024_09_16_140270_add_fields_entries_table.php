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
        if (!Schema::hasColumn('entries', 'ecclesiastical_divisions_groups_id'))
        {
            Schema::table('entries', function (Blueprint $table)
            {
                $table->integer('ecclesiastical_divisions_groups_id')->nullable()->after('recipient');

                $table->foreign('ecclesiastical_divisions_groups_id')
                    ->references('id')
                    ->on('ecclesiastical_divisions_groups');
            });
        }

        if (!Schema::hasColumn('entries', 'ecclesiastical_divisions_groups_devolution_origin'))
        {
            Schema::table('entries', function (Blueprint $table)
            {
                $table->integer('ecclesiastical_divisions_groups_devolution_origin')->nullable()->after('devolution');

                $table->foreign('ecclesiastical_divisions_groups_devolution_origin', 'fgk_ecclesiastical_divisions_groups_devolution_origin')
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
