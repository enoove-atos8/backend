<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Renomeia slug para route_resource se a coluna slug existir
        if (Schema::hasColumn('ecclesiastical_divisions', 'slug') && !Schema::hasColumn('ecclesiastical_divisions', 'route_resource')) {
            Schema::table('ecclesiastical_divisions', function (Blueprint $table) {
                $table->renameColumn('slug', 'route_resource');
            });
        }

        // Adiciona require_leader se não existir
        if (!Schema::hasColumn('ecclesiastical_divisions', 'require_leader')) {
            Schema::table('ecclesiastical_divisions', function (Blueprint $table) {
                $table->boolean('require_leader')->default(false)->after('enabled')->comment('1=exige líder, 0=não exige líder');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('ecclesiastical_divisions', 'require_leader')) {
            Schema::table('ecclesiastical_divisions', function (Blueprint $table) {
                $table->dropColumn('require_leader');
            });
        }

        if (Schema::hasColumn('ecclesiastical_divisions', 'route_resource') && !Schema::hasColumn('ecclesiastical_divisions', 'slug')) {
            Schema::table('ecclesiastical_divisions', function (Blueprint $table) {
                $table->renameColumn('route_resource', 'slug');
            });
        }
    }
};
