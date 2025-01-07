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
        if (!Schema::hasColumn('ecclesiastical_divisions_groups', 'financial_group'))
        {
            Schema::table('ecclesiastical_divisions_groups', function (Blueprint $table)
            {
                $table->boolean('financial_group')->nullable(false)->default(0)->after('return_receiving');
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
        Schema::dropIfExists('ecclesiastical_divisions_groups');
    }
};
