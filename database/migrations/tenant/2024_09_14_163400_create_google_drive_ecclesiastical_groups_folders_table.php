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
        if (!Schema::hasTable('google_drive_ecclesiastical_groups_folders'))
        {
            Schema::create('google_drive_ecclesiastical_groups_folders', function (Blueprint $table) {

                $table->integer('id', true)->autoIncrement();
                $table->integer('ecclesiastical_divisions_group_id')->nullable();
                $table->string('folder_id')->nullable(false);
                $table->string('folder_name')->nullable(false);
                $table->string('entry_type')->nullable();

                $table->timestamps();

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
