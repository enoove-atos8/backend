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
        if (!Schema::hasTable('entries'))
        {
            Schema::create('entries', function (Blueprint $table) {
                $table->integer('id', true)->comment('ID da entrada');
                $table->integer('member_id')->nullable()->comment('FK members.id - membro que contribuiu');
                $table->integer('reviewer_id')->nullable(false)->comment('FK financial_reviewers.id - revisor responsável');
                $table->string('entry_type')->nullable(false)->comment('Tipo: tithe=dízimo, offer=oferta, designated=designado');
                $table->string('transaction_type')->nullable()->comment('Forma de pagamento: pix, transfer, deposit, cash');
                $table->string('transaction_compensation')->nullable(false)->comment('Compensação da transação');
                $table->string('date_transaction_compensation')->nullable()->comment('Data da compensação formato YYYY-MM-DD');
                $table->string('date_entry_register')->nullable(false)->comment('Data do registro formato YYYY-MM-DD');
                $table->decimal('amount')->nullable(false)->comment('Valor em reais');
                $table->string('recipient')->nullable()->comment('Destinatário');
                $table->boolean('devolution')->default(0)->nullable()->comment('1=é devolução de valor');
                $table->boolean('residual_value')->default(0)->nullable();
                $table->boolean('deleted')->nullable(false)->default(0)->comment('1=registro deletado (soft delete)');
                $table->string('comments')->nullable()->comment('Observações');
                $table->text('receipt_link')->nullable();

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

            // Adiciona comentário na tabela
            \DB::statement("ALTER TABLE entries COMMENT 'Entradas financeiras: dízimos, ofertas e designados'");
        }
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
