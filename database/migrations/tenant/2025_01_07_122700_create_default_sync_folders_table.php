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
        if (!Schema::hasTable('default_sync_folders'))
        {
            Schema::create('default_sync_folders', function (Blueprint $table) {

                $table->integer('id', true);
                $table->integer('group_received_id')->nullable();
                $table->integer('started_by');
                $table->string('report_name')->nullable(false);
                $table->boolean('detailed_report')->nullable(false)->default(0);
                $table->timestamp('generation_date');
                $table->json('dates');
                $table->string('status');
                $table->string('error')->nullable();
                $table->json('entry_types');
                $table->string('link_report')->nullable();
                $table->boolean('date_order');
                $table->boolean('all_groups_receipts');


                $table->timestamps();

                $table->foreign('started_by')
                    ->references('id')
                    ->on('users');

                $table->foreign('group_received_id')
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
        Schema::dropIfExists('default_sync_folders');
    }
};
