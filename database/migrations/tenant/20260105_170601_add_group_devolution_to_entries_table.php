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
        if (!Schema::hasColumn('entries', 'group_devolution'))
        {
            Schema::table('entries', function (Blueprint $table)
            {
                $table->boolean('group_devolution')->default(0)->after('devolution')
                    ->comment('1=é devolução para o próprio grupo');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('entries', 'group_devolution'))
        {
            Schema::table('entries', function (Blueprint $table)
            {
                $table->dropColumn('group_devolution');
            });
        }
    }
};
