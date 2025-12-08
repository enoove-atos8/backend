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
    public function up()
    {
        if (Schema::hasTable('accounts')) {
            Schema::table('accounts', function (Blueprint $table) {
                $table->decimal('initial_balance', 10, 2)->nullable()->after('account_number')->comment('Saldo inicial em reais');
                $table->string('initial_balance_date')->nullable()->after('initial_balance')->comment('Data do saldo inicial formato YYYY-MM-DD');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('accounts')) {
            Schema::table('accounts', function (Blueprint $table) {
                $table->dropColumn(['initial_balance', 'initial_balance_date']);
            });
        }
    }
};
