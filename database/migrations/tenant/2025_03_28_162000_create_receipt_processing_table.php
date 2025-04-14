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
        if (!Schema::hasTable('receipt_processing'))
        {
            Schema::create('receipt_processing', function (Blueprint $table) {

                $table->id();
                $table->string('doc_type');
                $table->string('doc_sub_type');
                $table->integer('division_id')->nullable();
                $table->integer('group_returned_id')->nullable();
                $table->integer('group_received_id')->nullable();
                $table->integer('payment_category_id')->nullable();
                $table->integer('payment_item_id')->nullable();
                $table->decimal('amount')->nullable();
                $table->string('reason')->nullable();
                $table->string('status')->nullable();
                $table->string('institution')->nullable();
                $table->boolean('devolution');
                $table->boolean('is_payment');
                $table->integer('deleted')->nullable(false)->default(0);
                $table->string('receipt_link')->nullable(false);

                $table->timestamps();

                $table->foreign('division_id')
                    ->references('id')
                    ->on('ecclesiastical_divisions');

                $table->foreign('group_returned_id')
                    ->references('id')
                    ->on('ecclesiastical_divisions_groups');

                $table->foreign('group_received_id')
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
        Schema::dropIfExists('receipt_processing');
    }
};
