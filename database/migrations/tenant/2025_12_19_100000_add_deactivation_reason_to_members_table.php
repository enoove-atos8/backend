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
        Schema::table('members', function (Blueprint $table) {
            if (! Schema::hasColumn('members', 'deactivation_reason')) {
                $table->enum('deactivation_reason', [
                    'death',
                    'church_transfer',
                    'voluntary_withdrawal',
                    'exclusion',
                    'lost_contact',
                    'prolonged_inactivity',
                    'denomination_change',
                    'relocation',
                ])->nullable()->after('activated');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            if (Schema::hasColumn('members', 'deactivation_reason')) {
                $table->dropColumn('deactivation_reason');
            }
        });
    }
};
