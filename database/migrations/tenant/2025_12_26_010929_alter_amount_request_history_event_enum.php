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
        if (Schema::hasTable('amount_request_history')) {
            Schema::table('amount_request_history', function (Blueprint $table) {
                $table->dropColumn('event');
            });

            Schema::table('amount_request_history', function (Blueprint $table) {
                $table->enum('event', [
                    'created',
                    'updated',
                    'approved',
                    'rejected',
                    'transferred',
                    'exit_unlinked',
                    'receipt_added',
                    'receipt_removed',
                    'receipt_updated',
                    'receipt_deleted',
                    'reminder_sent',
                    'status_changed',
                    'closed',
                    'devolution_linked',
                ])->nullable(false)->after('amount_request_id')->comment('Tipo do evento');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('amount_request_history')) {
            Schema::table('amount_request_history', function (Blueprint $table) {
                $table->dropColumn('event');
            });

            Schema::table('amount_request_history', function (Blueprint $table) {
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
                ])->nullable(false)->after('amount_request_id')->comment('Tipo do evento');
            });
        }
    }
};
