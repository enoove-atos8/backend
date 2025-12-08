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
        if (! Schema::hasTable('exits')) {
            Schema::create('exits', function (Blueprint $table) {
                $table->integer('id', true)->comment('ID da saída');
                $table->integer('reviewer_id')->nullable()->comment('FK financial_reviewers.id - revisor responsável');
                $table->string('exit_type')->nullable(false)->comment('Tipo de saída');
                $table->integer('division_id')->nullable()->comment('FK ecclesiastical_divisions.id');
                $table->integer('group_id')->nullable()->comment('FK ecclesiastical_divisions_groups.id - grupo relacionado');
                $table->integer('payment_category_id')->nullable()->comment('FK payment_category.id - categoria do pagamento');
                $table->integer('payment_item_id')->nullable()->comment('FK payment_item.id - item do pagamento');
                $table->boolean('is_payment')->nullable()->comment('1=é pagamento de conta');
                $table->boolean('deleted')->nullable(false)->default(0)->comment('1=registro deletado (soft delete)');
                $table->string('transaction_type')->nullable(false)->comment('Forma: pix, transfer, debit, cash, credit_card');
                $table->string('transaction_compensation')->nullable(false)->comment('Compensação da transação');
                $table->string('date_transaction_compensation')->nullable(false)->comment('Data da compensação formato YYYY-MM-DD');
                $table->string('date_exit_register')->nullable(false)->comment('Data do registro formato YYYY-MM-DD');
                $table->string('timestamp_exit_transaction')->nullable();
                $table->decimal('amount', 10, 2)->nullable()->comment('Valor em reais');
                $table->string('comments')->nullable()->comment('Observações');
                $table->string('receipt_link')->nullable();

                $table->timestamps();

                $table->foreign('reviewer_id')
                    ->references('id')
                    ->on('financial_reviewers');

                $table->foreign('division_id')
                    ->references('id')
                    ->on('ecclesiastical_divisions');

                $table->foreign('group_id')
                    ->references('id')
                    ->on('ecclesiastical_divisions_groups');

                $table->foreign('payment_category_id')
                    ->references('id')
                    ->on('payment_category');

                $table->foreign('payment_item_id')
                    ->references('id')
                    ->on('payment_item');

            });

            // Adiciona comentário na tabela
            \DB::statement("ALTER TABLE exits COMMENT 'Saídas financeiras: despesas e pagamentos'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exits');
    }
};
