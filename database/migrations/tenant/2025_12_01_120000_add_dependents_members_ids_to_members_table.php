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
        if (Schema::hasTable('members') && !Schema::hasColumn('members', 'dependents_members_ids')) {
            Schema::table('members', function (Blueprint $table) {
                $table->json('dependents_members_ids')->nullable()->after('group_ids')->comment('Array de IDs dos membros dependentes');
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
        if (Schema::hasTable('members') && Schema::hasColumn('members', 'dependents_members_ids')) {
            Schema::table('members', function (Blueprint $table) {
                $table->dropColumn('dependents_members_ids');
            });
        }
    }
};
