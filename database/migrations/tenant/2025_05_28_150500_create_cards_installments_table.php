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
        if (!Schema::hasTable('cards_installments'))
        {
            Schema::create('cards_installments', function (Blueprint $table) {

                $table->id();
                $table->unsignedBigInteger('card_id');
                $table->string('status')->nullable(false); // Ex: 'open', 'closed', 'paid', 'overdue'
                $table->decimal('amount', 15)->nullable(false);
                $table->integer('installment')->nullable(false);
                $table->decimal('installment_amount', 15)->nullable(false);
                $table->string('date')->nullable(false);
                $table->boolean('deleted')->default(false);

                $table->timestamps();


                $table->foreign('card_id')
                    ->references('id')
                    ->on('cards');
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
        Schema::dropIfExists('cards_installments');
    }
};
