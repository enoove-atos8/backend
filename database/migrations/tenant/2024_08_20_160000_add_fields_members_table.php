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
        if (Schema::hasTable('members'))
        {
            if (!Schema::hasColumn('members', 'ecclesiastical_divisions_area_id'))
            {
                Schema::table('members', function (Blueprint $table)
                {
                    $table->integer('ecclesiastical_divisions_area_id')->nullable()->after('id');

                    // Relationships with ecclesiastical divisions areas
                    $table->foreign('ecclesiastical_divisions_area_id')
                        ->references('id')
                        ->on('ecclesiastical_divisions_areas')
                        ->onDelete('set null');
                });
            }

            if (!Schema::hasColumn('members', 'leader'))
            {
                Schema::table('members', function (Blueprint $table)
                {
                    $table->boolean('leader')->nullable()->default(0)->after('ecclesiastical_divisions_area_id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
