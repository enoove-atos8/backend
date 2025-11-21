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
        if (Schema::hasTable('plans')) {
            Schema::table('plans', function (Blueprint $table) {
                if (!Schema::hasColumn('plans', 'stripe_product_id')) {
                    $table->string('stripe_product_id')->unique()->nullable()->after('price');
                }

                if (!Schema::hasColumn('plans', 'stripe_price_id')) {
                    $table->string('stripe_price_id')->unique()->nullable()->after('stripe_product_id');
                }

                if (!Schema::hasColumn('plans', 'billing_interval')) {
                    $table->enum('billing_interval', ['month', 'year'])->default('month')->after('stripe_price_id');
                }

                if (!Schema::hasColumn('plans', 'trial_period_days')) {
                    $table->integer('trial_period_days')->default(0)->after('billing_interval');
                }

                if (!Schema::hasColumn('plans', 'features')) {
                    $table->json('features')->nullable()->after('trial_period_days');
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
        if (Schema::hasTable('plans')) {
            Schema::table('plans', function (Blueprint $table) {
                if (Schema::hasColumn('plans', 'stripe_product_id')) {
                    $table->dropColumn('stripe_product_id');
                }

                if (Schema::hasColumn('plans', 'stripe_price_id')) {
                    $table->dropColumn('stripe_price_id');
                }

                if (Schema::hasColumn('plans', 'billing_interval')) {
                    $table->dropColumn('billing_interval');
                }

                if (Schema::hasColumn('plans', 'trial_period_days')) {
                    $table->dropColumn('trial_period_days');
                }

                if (Schema::hasColumn('plans', 'features')) {
                    $table->dropColumn('features');
                }
            });
        }
    }
};
