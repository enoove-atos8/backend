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
        Schema::create('entries', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('member_id')->nullable();
            $table->integer('reviewer_id')->nullable(false);
            $table->string('entry_type')->nullable(false);
            $table->string('transaction_type')->nullable(false);
            $table->string('transaction_compensation')->nullable(false);
            $table->string('date_transaction_compensation')->nullable();
            $table->string('date_entry_register')->nullable(false);
            $table->decimal('amount')->nullable(false);
            $table->string('recipient')->nullable();
            $table->boolean('devolution')->default(0);
            $table->boolean('deleted')->nullable(false)->default(0);
            $table->string('comments')->nullable();
            $table->string('receipt_link')->nullable();

            // Relationships

            $table->foreign('member_id')
                ->references('id')
                ->on('members');

            // Relationships

            $table->foreign('reviewer_id')
                ->references('id')
                ->on('financial_reviewers');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entries');
    }
};
