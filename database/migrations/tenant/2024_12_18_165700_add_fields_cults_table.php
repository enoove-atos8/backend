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
        if (!Schema::hasColumn('cults', 'worship_without_entries'))
        {
            Schema::table('cults', function (Blueprint $table)
            {
                $table->boolean('worship_without_entries')->default(0)->after('reviewer_id')->comment('1=culto sem entradas registradas');
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
        Schema::dropIfExists('cults');
    }
};
