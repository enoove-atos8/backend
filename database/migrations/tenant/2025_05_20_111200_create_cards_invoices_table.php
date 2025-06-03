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
        if (!Schema::hasTable('cards_invoices'))
        {
            Schema::create('cards_invoices', function (Blueprint $table) {

                $table->id();
                $table->unsignedBigInteger('card_id');
                $table->string('status')->nullable(false); // Ex: 'open', 'closed', 'paid', 'overdue'
                $table->decimal('amount', 10)->nullable();
                $table->string('reference_date')->nullable(false);
                $table->string('payment_date')->nullable();
                $table->string('payment_method')->nullable();
                $table->boolean('is_closed')->default(false);
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
        Schema::dropIfExists('cards_invoices');
    }
};
