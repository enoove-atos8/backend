<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        if (!Schema::hasColumn('members', 'member_number'))
        {
            Schema::table('members', function (Blueprint $table)
            {
                $table->integer('member_number')->nullable()->after('id')->comment('Número do membro no rol');
            });
        }

        if (!Schema::hasColumn('members', 'tithers_list'))
        {
            Schema::table('members', function (Blueprint $table)
            {
                $table->boolean('tithers_list')->nullable()->after('member_number')->comment('1=está na lista de dizimistas regulares');
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
        Schema::dropIfExists('members');
    }
};
