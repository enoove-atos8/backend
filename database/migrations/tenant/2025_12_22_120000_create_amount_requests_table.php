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
        if (! Schema::hasTable('amount_requests')) {
            Schema::create('amount_requests', function (Blueprint $table) {
                $table->id()->comment('ID da solicitação de verba');
                $table->integer('member_id')->nullable(false)->comment('FK members.id - membro solicitante');
                $table->integer('group_id')->nullable(false)->comment('FK ecclesiastical_divisions_groups.id - grupo relacionado');
                $table->decimal('requested_amount', 10, 2)->nullable(false)->comment('Valor solicitado em reais');
                $table->text('description')->nullable(false)->comment('Finalidade/descrição da solicitação');
                $table->date('proof_deadline')->nullable(false)->comment('Prazo limite para comprovação');
                $table->enum('status', [
                    'pending',
                    'approved',
                    'rejected',
                    'transferred',
                    'partially_proven',
                    'proven',
                    'closed',
                    'overdue',
                ])->default('pending')->comment('Status da solicitação');
                $table->integer('approved_by')->nullable()->comment('FK users.id - usuário que aprovou');
                $table->timestamp('approved_at')->nullable()->comment('Data/hora da aprovação');
                $table->text('rejection_reason')->nullable()->comment('Motivo da rejeição');
                $table->integer('transfer_exit_id')->nullable()->comment('FK exits.id - saída financeira do repasse');
                $table->timestamp('transferred_at')->nullable()->comment('Data/hora da transferência');
                $table->decimal('proven_amount', 10, 2)->default(0)->comment('Valor comprovado em reais');
                $table->integer('devolution_entry_id')->nullable()->comment('FK entries.id - entrada da devolução');
                $table->decimal('devolution_amount', 10, 2)->default(0)->comment('Valor devolvido em reais');
                $table->integer('closed_by')->nullable()->comment('FK users.id - usuário que fechou');
                $table->timestamp('closed_at')->nullable()->comment('Data/hora do fechamento');
                $table->integer('requested_by')->nullable(false)->comment('FK users.id - usuário que criou a solicitação');
                $table->timestamps();
                $table->boolean('deleted')->default(false)->comment('1=registro deletado (soft delete)');

                $table->foreign('member_id')
                    ->references('id')
                    ->on('members');

                $table->foreign('group_id')
                    ->references('id')
                    ->on('ecclesiastical_divisions_groups');

                $table->foreign('approved_by')
                    ->references('id')
                    ->on('users');

                $table->foreign('transfer_exit_id')
                    ->references('id')
                    ->on('exits');

                $table->foreign('devolution_entry_id')
                    ->references('id')
                    ->on('entries');

                $table->foreign('closed_by')
                    ->references('id')
                    ->on('users');

                $table->foreign('requested_by')
                    ->references('id')
                    ->on('users');

                $table->index(['status', 'proof_deadline']);
                $table->index(['member_id', 'status']);
                $table->index(['group_id', 'status']);
            });

            \DB::statement("ALTER TABLE amount_requests COMMENT 'Solicitações de verbas para grupos e ministérios'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amount_requests');
    }
};
