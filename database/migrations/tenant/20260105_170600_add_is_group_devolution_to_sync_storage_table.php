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
        if (!Schema::hasColumn('sync_storage', 'is_group_devolution'))
        {
            Schema::table('sync_storage', function (Blueprint $table)
            {
                $table->boolean('is_group_devolution')->default(0)->after('is_devolution')
                    ->comment('1=é devolução para o próprio grupo');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('sync_storage', 'is_group_devolution'))
        {
            Schema::table('sync_storage', function (Blueprint $table)
            {
                $table->dropColumn('is_group_devolution');
            });
        }
    }
};
