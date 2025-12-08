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
        if (!Schema::hasTable('payment_item'))
        {
            Schema::create('payment_item', function (Blueprint $table) {

                $table->integer('id', true)->comment('ID do item');
                $table->integer('payment_category_id')->comment('FK payment_category.id');
                $table->string('slug')->comment('Slug para identificação');
                $table->string('name')->comment('Nome do item');
                $table->timestamps();

                $table->foreign('payment_category_id')
                    ->references('id')
                    ->on('payment_category');

            });

            // Adiciona comentário na tabela
            DB::statement("ALTER TABLE payment_item COMMENT 'Itens de pagamento dentro de cada categoria'");
        }
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_item');
    }
};
