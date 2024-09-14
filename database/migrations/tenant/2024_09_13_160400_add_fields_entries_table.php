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
        if (!Schema::hasColumn('entries', 'identification_pending'))
        {
            Schema::table('entries', function (Blueprint $table)
            {
                $table->boolean('identification_pending')->nullable()->after('reviewer_id');
            });
        }

        if (!Schema::hasColumn('entries', 'timestamp_value_cpf'))
        {
            Schema::table('entries', function (Blueprint $table)
            {
                $table->string('timestamp_value_cpf')->nullable()->after('recipient');
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
