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
        Schema::create('transactions', function (Blueprint $table) {

            $table->integer('id', true);
            $table->integer('account_bank_integration_id');
            $table->string('operation', 255)->nullable(false);
            $table->string('date')->nullable(false);
            $table->string('nr_doc')->nullable(false);
            $table->string('history', 255)->nullable(false);
            $table->string('type', 11)->nullable();
            $table->float('value')->nullable(false);
            $table->float('amount')->nullable(false);
            $table->string('type_deposit', 10)->nullable();

            $table->foreign('account_bank_integration_id')
                ->references('id')
                ->on('account_bank_integrations');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
