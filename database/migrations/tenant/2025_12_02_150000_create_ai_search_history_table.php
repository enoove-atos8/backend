<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_search_history', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('FK users.id - usuário que fez a pergunta');
            $table->text('question')->comment('Pergunta feita pelo usuário');
            $table->text('sql_generated')->nullable()->comment('SQL gerado pela IA');
            $table->json('result_data')->nullable()->comment('Dados retornados pela query');
            $table->string('result_title')->nullable()->comment('Título formatado da resposta');
            $table->text('result_description')->nullable()->comment('Descrição explicativa da resposta');
            $table->string('suggested_followup')->nullable()->comment('Sugestão de pergunta complementar');
            $table->integer('execution_time_ms')->nullable()->comment('Tempo de execução em ms');
            $table->boolean('success')->default(true)->comment('Se a consulta foi bem sucedida');
            $table->text('error_message')->nullable()->comment('Mensagem de erro se falhou');
            $table->timestamps();

            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_search_history');
    }
};
