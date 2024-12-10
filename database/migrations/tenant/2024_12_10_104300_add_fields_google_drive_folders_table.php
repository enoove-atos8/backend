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
        if (!Schema::hasColumn('google_drive_ecclesiastical_groups_folders', 'tenant'))
        {
            Schema::table('google_drive_ecclesiastical_groups_folders', function (Blueprint $table)
            {
                $table->string('tenant')->nullable(false)->after('id');
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
        Schema::dropIfExists('google_drive_ecclesiastical_groups_folders');
    }
};
