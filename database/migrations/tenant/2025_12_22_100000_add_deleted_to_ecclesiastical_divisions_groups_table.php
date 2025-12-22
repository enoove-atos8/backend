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
        if (!Schema::hasColumn('ecclesiastical_divisions_groups', 'deleted')) {
            Schema::table('ecclesiastical_divisions_groups', function (Blueprint $table) {
                $table->boolean('deleted')->default(false)->after('enabled');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('ecclesiastical_divisions_groups', 'deleted')) {
            Schema::table('ecclesiastical_divisions_groups', function (Blueprint $table) {
                $table->dropColumn('deleted');
            });
        }
    }
};
