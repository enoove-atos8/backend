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
        if (!Schema::hasColumn('google_drive_ecclesiastical_groups_folders', 'folder_devolution'))
        {
            Schema::table('google_drive_ecclesiastical_groups_folders', function (Blueprint $table)
            {
                $table->boolean('folder_devolution')->nullable(false)->default(0) ->after('folder_name');
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
