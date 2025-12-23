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
        if (! Schema::hasTable('amount_request_receipts')) {
            Schema::create('amount_request_receipts', function (Blueprint $table) {
                $table->id()->comment('ID do comprovante');
                $table->unsignedBigInteger('amount_request_id')->nullable(false)->comment('FK amount_requests.id');
                $table->decimal('amount', 10, 2)->nullable(false)->comment('Valor do comprovante em reais');
                $table->string('description')->nullable(false)->comment('Descrição do comprovante');
                $table->string('image_url')->nullable(false)->comment('URL da imagem do comprovante');
                $table->date('receipt_date')->nullable(false)->comment('Data do comprovante');
                $table->integer('created_by')->nullable(false)->comment('FK users.id - usuário que criou');
                $table->timestamps();
                $table->boolean('deleted')->default(false)->comment('1=registro deletado (soft delete)');

                $table->foreign('amount_request_id')
                    ->references('id')
                    ->on('amount_requests')
                    ->onDelete('cascade');

                $table->foreign('created_by')
                    ->references('id')
                    ->on('users');

                $table->index(['amount_request_id', 'deleted']);
            });

            \DB::statement("ALTER TABLE amount_request_receipts COMMENT 'Comprovantes de gastos das solicitações de verbas'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amount_request_receipts');
    }
};
