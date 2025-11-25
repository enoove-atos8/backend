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
        if (Schema::hasTable('plans')) {
            Schema::table('plans', function (Blueprint $table) {
                if (! Schema::hasColumn('plans', 'billing_unit')) {
                    $table->boolean('billing_unit')->default(false)->after('stripe_price_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('plans')) {
            Schema::table('plans', function (Blueprint $table) {
                if (Schema::hasColumn('plans', 'billing_unit')) {
                    $table->dropColumn('billing_unit');
                }
            });
        }
    }
};
