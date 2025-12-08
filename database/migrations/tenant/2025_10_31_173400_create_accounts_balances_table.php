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

                $table->id()->comment('ID do saldo');
                $table->unsignedBigInteger('account_id')->nullable(false)->comment('FK accounts.id');
                $table->string('reference_date')->nullable(false)->comment('Mês de referência formato YYYY-MM');
                $table->decimal('previous_month_balance', 10, 2)->nullable()->comment('Saldo do mês anterior em reais');
                $table->decimal('current_month_balance', 10, 2)->nullable()->comment('Saldo atual do mês em reais');
                $table->boolean('is_initial_balance')->nullable(false)->default(false);
                $table->boolean('deleted')->nullable(false)->default(0)->comment('1=registro deletado (soft delete)');

                $table->timestamps();

                $table->foreign('account_id')
                    ->references('id')
                    ->on('accounts');
            });

            // Adiciona comentário na tabela
            \DB::statement("ALTER TABLE accounts_balances COMMENT 'Saldos mensais das contas bancárias'");

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
