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
        if (Schema::hasTable('exits')) {
            Schema::table('exits', function (Blueprint $table) {
                $table->boolean('duplicity_verified')->default(0)->after('timestamp_exit_transaction')->comment('1=duplicidade verificada manualmente');
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
        if (Schema::hasTable('exits')) {
            Schema::table('exits', function (Blueprint $table) {
                $table->dropColumn('duplicity_verified');
            });
        }
    }
};
