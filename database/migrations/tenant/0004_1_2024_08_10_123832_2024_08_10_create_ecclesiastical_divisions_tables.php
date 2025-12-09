<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('ecclesiastical_divisions'))
        {
            Schema::create('ecclesiastical_divisions', function (Blueprint $table) {

                $table->integer('id', true)->autoIncrement()->comment('ID da divisão');
                $table->string('route_resource')->nullable(false)->comment('Slug/identificador da rota');
                $table->string('name')->nullable(false)->comment('Nome da divisão');
                $table->string('description')->nullable()->comment('Descrição da divisão');
                $table->boolean('enabled')->nullable(false)->default(1)->comment('1=divisão ativa, 0=divisão inativa');
                $table->boolean('require_leader')->default(false)->comment('1=exige líder, 0=não exige líder');

                $table->timestamps();
            });

            // Adiciona comentário na tabela
            \DB::statement("ALTER TABLE ecclesiastical_divisions COMMENT 'Divisões eclesiásticas (categorias de grupos)'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ecclesiastical_divisions');
    }
};
