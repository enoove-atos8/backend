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
        if (!Schema::hasTable('accounts'))
        {
            Schema::create('accounts', function (Blueprint $table) {

                $table->id()->comment('ID da conta');
                $table->string('account_type')->nullable(false)->comment('Tipo: checking=corrente, savings=poupança');
                $table->string('bank_name')->nullable(false)->comment('Nome do banco');
                $table->string('agency_number')->nullable(false)->comment('Número da agência');
                $table->string('account_number')->nullable(false)->comment('Número da conta');
                $table->boolean('activated')->default(true)->comment('1=conta ativa, 0=conta inativa');

                $table->timestamps();
            });

            // Adiciona comentário na tabela
            \DB::statement("ALTER TABLE accounts COMMENT 'Contas bancárias da igreja'");

        };
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
};
