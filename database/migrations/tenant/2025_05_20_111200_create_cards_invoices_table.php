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

                $table->id()->comment('ID da fatura');
                $table->unsignedBigInteger('card_id')->comment('FK cards.id');
                $table->string('status')->nullable(false)->comment('Status: open=aberta, closed=fechada, paid=paga');
                $table->decimal('amount', 10)->nullable()->comment('Valor total da fatura em reais');
                $table->string('reference_date')->nullable(false)->comment('Mês de referência formato YYYY-MM');
                $table->string('payment_date')->nullable()->comment('Data do pagamento formato YYYY-MM-DD');
                $table->string('payment_method')->nullable();
                $table->boolean('is_closed')->default(false)->comment('1=fatura fechada');
                $table->boolean('deleted')->default(false)->comment('1=registro deletado (soft delete)');


                $table->timestamps();


                $table->foreign('card_id')
                    ->references('id')
                    ->on('cards');
            });

            // Adiciona comentário na tabela
            \DB::statement("ALTER TABLE cards_invoices COMMENT 'Faturas dos cartões de crédito'");

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
