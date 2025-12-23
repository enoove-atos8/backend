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
        if (! Schema::hasTable('amount_request_reminders')) {
            Schema::create('amount_request_reminders', function (Blueprint $table) {
                $table->id()->comment('ID do lembrete');
                $table->unsignedBigInteger('amount_request_id')->nullable(false)->comment('FK amount_requests.id');
                $table->enum('type', [
                    'request_created',
                    'request_approved',
                    'request_rejected',
                    'transfer_completed',
                    'proof_reminder',
                    'proof_urgent',
                    'proof_overdue',
                    'proof_received',
                    'devolution_required',
                    'request_closed',
                ])->nullable(false)->comment('Tipo do lembrete');
                $table->enum('channel', [
                    'whatsapp',
                    'email',
                    'system',
                ])->nullable(false)->comment('Canal de envio');
                $table->timestamp('scheduled_at')->nullable()->comment('Data/hora agendada para envio');
                $table->timestamp('sent_at')->nullable()->comment('Data/hora do envio');
                $table->enum('status', [
                    'pending',
                    'sent',
                    'failed',
                    'delivered',
                    'read',
                ])->default('pending')->comment('Status do lembrete');
                $table->text('error_message')->nullable()->comment('Mensagem de erro se falhou');
                $table->json('metadata')->nullable()->comment('Dados adicionais em JSON');
                $table->timestamps();

                $table->foreign('amount_request_id')
                    ->references('id')
                    ->on('amount_requests')
                    ->onDelete('cascade');

                $table->index(['amount_request_id', 'status']);
                $table->index(['status', 'scheduled_at']);
                $table->index(['type', 'status']);
            });

            \DB::statement("ALTER TABLE amount_request_reminders COMMENT 'Lembretes e notificações das solicitações de verbas'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amount_request_reminders');
    }
};
