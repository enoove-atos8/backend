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
        if (!Schema::hasTable('accounts_movements'))
        {
            Schema::create('accounts_movements', function (Blueprint $table) {

                $table->id();
                $table->unsignedBigInteger('account_id');
                $table->string('movement_date')->nullable(false);
                $table->string('transaction_type')->nullable();
                $table->string('description')->nullable();
                $table->decimal('amount')->nullable(false);
                $table->string('movement_type')->nullable(false);
                $table->boolean('anonymous')->nullable();
                $table->string('conciliated_status')->nullable();

                $table->timestamps();

                $table->foreign('account_id')
                    ->references('id')
                    ->on('accounts');
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
        Schema::dropIfExists('accounts_movements');
    }
};
