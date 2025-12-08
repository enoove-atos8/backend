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
        if (!Schema::hasTable('user_details'))
        {
            Schema::create('user_details', function (Blueprint $table) {
                $table->integer('id', true)->comment('ID do detalhe');
                $table->integer('user_id')->nullable(false)->comment('FK users.id');
                $table->string('full_name')->nullable(false)->comment('Nome completo');
                $table->string('avatar')->nullable();
                $table->string('type')->nullable();
                $table->string('title')->nullable();
                $table->string('gender')->nullable()->comment('Gênero: masculino ou feminino');
                $table->string('phone')->nullable()->comment('Telefone');
                $table->string('address')->nullable();
                $table->string('district')->nullable();
                $table->string('city')->nullable()->comment('Cidade');
                $table->string('country')->nullable();
                $table->string('birthday')->nullable();

                // Relationships

                $table->foreign('user_id')
                    ->references('id')
                    ->on('users');


                $table->timestamps();
            });

            // Adiciona comentário na tabela
            \DB::statement("ALTER TABLE user_details COMMENT 'Detalhes dos usuários do sistema'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
