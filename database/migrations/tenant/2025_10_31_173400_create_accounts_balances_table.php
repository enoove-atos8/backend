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
        if (! Schema::hasTable('accounts_balances')) {
            Schema::create('accounts_balances', function (Blueprint $table) {

                $table->id();
                $table->unsignedBigInteger('account_id')->nullable(false);
                $table->string('reference_date')->nullable(false);
                $table->decimal('previous_month_balance', 10, 2)->nullable();
                $table->decimal('current_month_balance', 10, 2)->nullable();
                $table->boolean('is_initial_balance')->nullable(false)->default(false);
                $table->boolean('deleted')->nullable(false)->default(0);

                $table->timestamps();

                $table->foreign('account_id')
                    ->references('id')
                    ->on('accounts');
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
        Schema::dropIfExists('accounts_balances');
    }
};
