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
        if (!Schema::hasTable('users'))
        {
            Schema::create('users', function (Blueprint $table) {
                $table->integer('id', true)->comment('ID do usuário');
                $table->string('email')->unique()->comment('E-mail de login');
                $table->string('password');
                $table->integer('activated')->default('0')->comment('1=usuário ativo, 0=usuário inativo');
                $table->boolean('changed_password')->default(false);
                $table->integer('access_quantity')->default(0);
                $table->string('type')->nullable()->comment('Tipo de usuário');

                $table->rememberToken();
                $table->timestamps();
            });

            // Adiciona comentário na tabela
            \DB::statement("ALTER TABLE users COMMENT 'Usuários do sistema'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
