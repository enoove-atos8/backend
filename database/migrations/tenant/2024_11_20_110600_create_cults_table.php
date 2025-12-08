<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        if (!Schema::hasTable('cults'))
        {
            Schema::create('cults', function (Blueprint $table) {

                $table->integer('id', true)->comment('ID do culto');
                $table->integer('reviewer_id')->nullable()->comment('FK financial_reviewers.id - revisor responsável');
                $table->string('cult_day')->nullable()->comment('Dia do culto: domingo, quarta, sexta, sabado');
                $table->string('cult_date')->nullable()->comment('Data do culto formato YYYY-MM-DD');
                $table->string('date_transaction_compensation')->nullable()->comment('Data da compensação formato YYYY-MM-DD');
                $table->string('transaction_type')->nullable()->comment('Tipo de transação');
                $table->decimal('tithes_amount')->nullable()->default(0)->comment('Total de dízimos arrecadados no culto');
                $table->decimal('designated_amount')->nullable()->default(0)->comment('Total de designados arrecadados no culto');
                $table->decimal('offer_amount')->nullable()->default(0)->comment('Total de ofertas arrecadadas no culto');
                $table->boolean('deleted')->nullable()->default(0)->comment('1=registro deletado (soft delete)');
                $table->string('receipt')->nullable();
                $table->string('comments')->nullable()->comment('Observações');

                $table->timestamps();

            });

            // Adiciona comentário na tabela
            DB::statement("ALTER TABLE cults COMMENT 'Cultos realizados'");

            if (!Schema::hasColumn('entries', 'cult_id'))
            {
                Schema::table('entries', function (Blueprint $table)
                {
                    $table->integer('cult_id')->nullable()->after('reviewer_id')->comment('FK cults.id - culto relacionado');

                    $table->foreign('cult_id')
                        ->references('id')
                        ->on('cults');
                });
            }
        }
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('cults');
    }
};
