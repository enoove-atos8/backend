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
        if (!Schema::hasTable('purchases_reports_request')) {
            Schema::create('purchases_reports_request', function (Blueprint $table) {

                $table->id();
                $table->unsignedBigInteger('card_id')->nullable();
                $table->integer('started_by');
                $table->string('report_name')->nullable(false);
                $table->boolean('detailed_report')->nullable(false)->default(0);
                $table->timestamp('generation_date');
                $table->json('dates');
                $table->string('status');
                $table->string('error')->nullable();
                $table->string('link_report')->nullable();
                $table->boolean('date_order')->nullable();
                $table->boolean('all_cards_receipts')->nullable()->default(false);
                $table->decimal('amount', 15, 2)->nullable();

                $table->timestamps();

                $table->foreign('card_id')
                    ->references('id')
                    ->on('cards');

                $table->foreign('started_by')
                    ->references('id')
                    ->on('users');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchases_reports_request');
    }
};
