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
        if (! Schema::hasTable('amount_request_history')) {
            Schema::create('amount_request_history', function (Blueprint $table) {
                $table->id()->comment('ID do registro de historico');
                $table->unsignedBigInteger('amount_request_id')->nullable(false)->comment('FK amount_requests.id');
                $table->enum('event', [
                    'created',
                    'updated',
                    'approved',
                    'rejected',
                    'transferred',
                    'receipt_added',
                    'receipt_removed',
                    'receipt_updated',
                    'reminder_sent',
                    'status_changed',
                    'closed',
                ])->nullable(false)->comment('Tipo do evento');
                $table->string('description', 500)->nullable(false)->comment('Descricao do evento');
                $table->integer('user_id')->nullable()->comment('FK users.id - Usuario que realizou a acao');
                $table->json('metadata')->nullable()->comment('Dados adicionais em JSON');
                $table->timestamps();

                $table->foreign('amount_request_id')
                    ->references('id')
                    ->on('amount_requests')
                    ->onDelete('cascade');

                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');
                $table->index(['amount_request_id', 'created_at']);
                $table->index(['event']);
            });

            \DB::statement("ALTER TABLE amount_request_history COMMENT 'Historico de eventos das solicitacoes de verbas'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amount_request_history');
    }
};
