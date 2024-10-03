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
        if (!Schema::hasColumn('entries', 'cult_entries'))
        {
            Schema::table('entries', function (Blueprint $table)
            {
                $table->boolean('cult_entries')->default(0)->after('identification_pending');
            });
        }

        if (!Schema::hasColumn('entries', 'pending_receipt'))
        {
            Schema::table('entries', function (Blueprint $table)
            {
                $table->boolean('pending_receipt')->default(0)->after('reviewer_id');
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
