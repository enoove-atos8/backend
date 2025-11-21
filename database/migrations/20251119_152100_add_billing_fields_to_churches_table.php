<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        if (Schema::hasTable('churches')) {
            Schema::table('churches', function (Blueprint $table) {
                if (!Schema::hasColumn('churches', 'stripe_id')) {
                    $table->string('stripe_id')->nullable()->index()->after('plan_id');
                }

                if (!Schema::hasColumn('churches', 'pm_type')) {
                    $table->string('pm_type')->nullable()->after('stripe_id');
                }

                if (!Schema::hasColumn('churches', 'pm_last_four')) {
                    $table->string('pm_last_four', 4)->nullable()->after('pm_type');
                }

                if (!Schema::hasColumn('churches', 'trial_ends_at')) {
                    $table->timestamp('trial_ends_at')->nullable()->after('pm_last_four');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (Schema::hasTable('churches')) {
            Schema::table('churches', function (Blueprint $table) {
                if (Schema::hasColumn('churches', 'stripe_id')) {
                    $table->dropColumn('stripe_id');
                }

                if (Schema::hasColumn('churches', 'pm_type')) {
                    $table->dropColumn('pm_type');
                }

                if (Schema::hasColumn('churches', 'pm_last_four')) {
                    $table->dropColumn('pm_last_four');
                }

                if (Schema::hasColumn('churches', 'trial_ends_at')) {
                    $table->dropColumn('trial_ends_at');
                }
            });
        }
    }
};
