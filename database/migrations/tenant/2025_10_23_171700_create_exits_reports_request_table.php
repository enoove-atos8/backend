<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('exits_reports_request'))
        {
            Schema::create('exits_reports_request', function (Blueprint $table) {

                $table->id();
                $table->unsignedBigInteger('account_id')->nullable();
                $table->integer('started_by');
                $table->string('report_name')->nullable(false);
                $table->boolean('detailed_report')->nullable(false)->default(0);
                $table->timestamp('generation_date');
                $table->json('dates');
                $table->string('status');
                $table->string('error')->nullable();
                $table->json('exit_types')->nullable();
                $table->string('link_report')->nullable();
                $table->boolean('date_order')->nullable();

                $table->timestamps();

                $table->foreign('account_id')
                    ->references('id')
                    ->on('accounts');

                $table->foreign('started_by')
                    ->references('id')
                    ->on('users');
            });

        };
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exits_reports_request');
    }
};
