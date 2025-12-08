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
        if (Schema::hasTable('members')) {
            Schema::table('members', function (Blueprint $table) {
                $table->json('group_ids')->nullable()->after('member_number')->comment('Array de IDs dos grupos que o membro participa');
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
        if (Schema::hasTable('members')) {
            Schema::table('members', function (Blueprint $table) {
                $table->dropColumn('group_ids');
            });
        }
    }
};
