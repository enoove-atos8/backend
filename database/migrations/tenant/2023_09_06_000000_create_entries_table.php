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
            $table->string('entry_type')->nullable(false);
            $table->string('transaction_type')->nullable(false);
            $table->string('transaction_compensation')->nullable(false);
            $table->string('date_transaction_compensation')->nullable();
            $table->string('date_entry_register')->nullable(false);
            $table->decimal('amount')->nullable(false);
            $table->string('recipient')->nullable();
            $table->boolean('devolution')->nullable()->default(0);
            $table->integer('member_id')->nullable();
            $table->integer('reviewer_id')->nullable(false);
            $table->boolean('deleted')->nullable(false)->default(0);
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
