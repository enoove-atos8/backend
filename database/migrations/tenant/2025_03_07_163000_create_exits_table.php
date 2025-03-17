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
        if (!Schema::hasTable('exits'))
        {
            Schema::create('exits', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('reviewer_id')->nullable(false);
                $table->string('exit_type')->nullable(false);
                $table->integer('division_id')->nullable();
                $table->integer('group_id')->nullable();
                $table->integer('payment_category_id')->nullable();
                $table->integer('payment_item_id')->nullable();
                $table->boolean('is_payment')->nullable();
                $table->boolean('deleted')->nullable(false)->default(0);
                $table->string('transaction_type')->nullable(false);
                $table->string('transaction_compensation')->nullable(false);
                $table->string('date_transaction_compensation')->nullable(false);
                $table->string('date_exit_register')->nullable(false);
                $table->string('timestamp_exit_transaction')->nullable(false);
                $table->decimal('amount', 10, 2)->nullable();
                $table->string('comments')->nullable();
                $table->string('receipt_link')->nullable(false);

                $table->timestamps();

                $table->foreign('reviewer_id')
                    ->references('id')
                    ->on('financial_reviewers');

                $table->foreign('division_id')
                    ->references('id')
                    ->on('ecclesiastical_divisions');

                $table->foreign('group_id')
                    ->references('id')
                    ->on('ecclesiastical_divisions_groups');

                $table->foreign('payment_category_id')
                    ->references('id')
                    ->on('payment_category');

                $table->foreign('payment_item_id')
                    ->references('id')
                    ->on('payment_item');

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
        Schema::dropIfExists('exits');
    }
};
