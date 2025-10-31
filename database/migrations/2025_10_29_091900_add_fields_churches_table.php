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
    public function up(): void
    {
        if (!Schema::hasColumn('churches', 'address'))
        {
            Schema::table('churches', function (Blueprint $table)
            {
                $table->string('address')->nullable()->after('logo');
            });
        }

        if (!Schema::hasColumn('churches', 'cell_phone'))
        {
            Schema::table('churches', function (Blueprint $table)
            {
                $table->string('cell_phone')->nullable()->after('address');
            });
        }

        if (!Schema::hasColumn('churches', 'mail'))
        {
            Schema::table('churches', function (Blueprint $table)
            {
                $table->string('mail')->nullable()->after('cell_phone');
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
        Schema::table('churches', function (Blueprint $table)
        {
            if (Schema::hasColumn('churches', 'address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('churches', 'cell_phone')) {
                $table->dropColumn('cell_phone');
            }
            if (Schema::hasColumn('churches', 'mail')) {
                $table->dropColumn('mail');
            }
        });
    }
};
