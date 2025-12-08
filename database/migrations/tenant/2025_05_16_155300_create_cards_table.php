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
        if (!Schema::hasTable('cards'))
        {
            Schema::create('cards', function (Blueprint $table) {

                $table->id()->comment('ID do cartão');
                $table->string('name')->comment('Nome identificador do cartão');
                $table->text('description')->nullable()->comment('Descrição do cartão');
                $table->string('card_number')->nullable(false)->comment('Últimos dígitos do cartão');
                $table->string('expiry_date')->nullable(false);
                $table->string('due_day')->nullable(false)->comment('Dia de vencimento da fatura');
                $table->string('closing_day')->nullable(false)->comment('Dia de fechamento da fatura');
                $table->string('status')->nullable(false);
                $table->boolean('active')->default(true)->comment('1=cartão ativo, 0=cartão inativo');
                $table->boolean('deleted')->default(false)->comment('1=registro deletado (soft delete)');
                $table->string('credit_card_brand')->nullable(false)->comment('Bandeira: visa, mastercard, elo, amex');
                $table->string('person_type')->nullable();
                $table->string('card_holder_name')->nullable();
                $table->decimal('limit', 15)->nullable(false)->comment('Limite do cartão em reais');

                $table->timestamps();
            });

            // Adiciona comentário na tabela
            \DB::statement("ALTER TABLE cards COMMENT 'Cartões de crédito da igreja'");

        };
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cards');
    }
};
