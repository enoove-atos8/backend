<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alterar ENUM para incluir proof_in_progress e proof_completed
        DB::statement("ALTER TABLE amount_request_reminders MODIFY COLUMN type ENUM(
            'request_created',
            'request_approved',
            'request_rejected',
            'transfer_completed',
            'proof_reminder',
            'proof_urgent',
            'proof_overdue',
            'proof_received',
            'proof_in_progress',
            'proof_completed',
            'devolution_required',
            'request_closed'
        ) NOT NULL COMMENT 'Tipo do lembrete'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter para ENUM original sem proof_in_progress e proof_completed
        DB::statement("ALTER TABLE amount_request_reminders MODIFY COLUMN type ENUM(
            'request_created',
            'request_approved',
            'request_rejected',
            'transfer_completed',
            'proof_reminder',
            'proof_urgent',
            'proof_overdue',
            'proof_received',
            'devolution_required',
            'request_closed'
        ) NOT NULL COMMENT 'Tipo do lembrete'");
    }
};
