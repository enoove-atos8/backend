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
        if (Schema::hasTable('members') && !Schema::hasColumn('members', 'dependent_member_id')) {
            Schema::table('members', function (Blueprint $table) {
                $table->unsignedInteger('dependent_member_id')->nullable()->after('group_ids');
                $table->foreign('dependent_member_id')->references('id')->on('members')->onDelete('SET NULL');
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
                $table->dropForeign(['dependent_member_id']);
                $table->dropColumn('dependent_member_id');
            });
        }
    }
};
