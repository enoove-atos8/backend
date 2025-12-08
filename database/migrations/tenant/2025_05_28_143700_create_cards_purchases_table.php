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
        if (!Schema::hasTable('cards_purchases'))
        {
            Schema::create('cards_purchases', function (Blueprint $table) {

                $table->id()->comment('ID da compra');
                $table->unsignedBigInteger('card_id')->comment('FK cards.id');
                $table->string('status')->nullable(false);
                $table->decimal('amount', 10)->nullable(false)->comment('Valor total da compra em reais');
                $table->integer('installments')->nullable(false)->comment('Número de parcelas');
                $table->decimal('installment_amount', 10)->nullable(false)->comment('Valor de cada parcela em reais');
                $table->string('date')->nullable(false)->comment('Data da compra formato YYYY-MM-DD');
                $table->boolean('deleted')->default(false)->comment('1=registro deletado (soft delete)');


                $table->timestamps();


                $table->foreign('card_id')
                    ->references('id')
                    ->on('cards');
            });

            // Adiciona comentário na tabela
            \DB::statement("ALTER TABLE cards_purchases COMMENT 'Compras realizadas no cartão de crédito'");

        };
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cards_purchases');
    }
};
