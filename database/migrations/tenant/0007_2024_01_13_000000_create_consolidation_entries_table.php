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
        if (!Schema::hasTable('consolidation_entries'))
        {
            Schema::create('consolidation_entries', function (Blueprint $table) {
                $table->integer('id', true)->comment('ID da consolidação');
                $table->string('date')->nullable(false)->comment('Data da consolidação formato YYYY-MM-DD');
                $table->boolean('consolidated')->nullable(false)->default(false)->comment('1=consolidado');
                $table->decimal('designated_amount')->nullable()->default(0)->comment('Total de designados do dia');
                $table->decimal('offers_amount')->nullable()->default(0)->comment('Total de ofertas do dia');
                $table->decimal('tithe_amount')->nullable()->default(0)->comment('Total de dízimos do dia');
                $table->decimal('total_amount')->nullable()->default(0)->comment('Total geral do dia');
                $table->decimal('monthly_target')->nullable(false)->default(0)->comment('Meta mensal em reais');

                $table->timestamps();
            });

            // Adiciona comentário na tabela
            \DB::statement("ALTER TABLE consolidation_entries COMMENT 'Consolidação diária de entradas financeiras'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consolidation_entries');
    }
};
