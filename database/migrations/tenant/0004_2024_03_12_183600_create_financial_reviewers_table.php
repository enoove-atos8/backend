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
    public function up(): void
    {
        if (!Schema::hasTable('financial_reviewers'))
        {
            Schema::create('financial_reviewers', function (Blueprint $table) {

                $table->integer('id', true)->autoIncrement()->comment('ID do revisor');
                $table->string('full_name')->nullable(false)->comment('Nome completo');
                $table->string('reviewer_type')->nullable(false)->comment('Tipo de revisor');
                $table->string('avatar')->nullable();
                $table->string('gender')->nullable(false)->comment('Gênero: masculino ou feminino');
                $table->string('cpf')->nullable()->unique()->comment('CPF');
                $table->string('rg')->nullable()->unique();
                $table->string('email')->unique()->nullable()->comment('E-mail');
                $table->string('cell_phone')->nullable(false)->comment('Celular');
                $table->integer('activated')->default('0')->comment('1=ativo, 0=inativo');
                $table->integer('deleted')->default('0')->comment('1=registro deletado (soft delete)');


                $table->rememberToken();
                $table->timestamps();
            });

            // Adiciona comentário na tabela
            \DB::statement("ALTER TABLE financial_reviewers COMMENT 'Revisores financeiros (tesoureiros)'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_reviewers');
    }
};
