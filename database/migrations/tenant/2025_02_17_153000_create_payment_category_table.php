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
        if (!Schema::hasTable('payment_category'))
        {
            Schema::create('payment_category', function (Blueprint $table) {

                $table->integer('id', true)->comment('ID da categoria');
                $table->string('slug')->unique()->comment('Slug para identificação');
                $table->string('name')->comment('Nome da categoria');
                $table->timestamps();

            });

            // Adiciona comentário na tabela
            DB::statement("ALTER TABLE payment_category COMMENT 'Categorias de pagamento para despesas'");
        }
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_category');
    }
};
