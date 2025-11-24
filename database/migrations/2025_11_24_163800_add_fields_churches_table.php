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
        if (Schema::hasTable('churches')) {
            Schema::table('churches', function (Blueprint $table) {
                if (! Schema::hasColumn('churches', 'member_count')) {
                    $table->integer('member_count')->nullable()->after('stripe_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('churches')) {
            Schema::table('churches', function (Blueprint $table) {
                if (Schema::hasColumn('churches', 'member_count')) {
                    $table->dropColumn('member_count');
                }
            });
        }
    }
};
