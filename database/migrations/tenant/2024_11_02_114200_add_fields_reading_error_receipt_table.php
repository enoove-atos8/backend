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
        if (!Schema::hasColumn('reading_error_receipt', 'group_received_id'))
        {
            Schema::table('reading_error_receipt', function (Blueprint $table)
            {
                $table->integer('group_received_id')->nullable()->after('id');

                $table->foreign('group_received_id')
                    ->references('id')
                    ->on('ecclesiastical_divisions_groups');
            });
        }

        if (!Schema::hasColumn('reading_error_receipt', 'group_returned_id'))
        {
            Schema::table('reading_error_receipt', function (Blueprint $table)
            {
                $table->integer('group_returned_id')->nullable()->after('id');

                $table->foreign('group_returned_id')
                    ->references('id')
                    ->on('ecclesiastical_divisions_groups');
            });
        }

        if (!Schema::hasColumn('reading_error_receipt', 'devolution'))
        {
            Schema::table('reading_error_receipt', function (Blueprint $table)
            {
                $table->boolean('devolution')->default(0)->after('amount');
            });
        }

        if (!Schema::hasColumn('reading_error_receipt', 'reason'))
        {
            Schema::table('reading_error_receipt', function (Blueprint $table)
            {
                $table->string('reason')->nullable(false)->after('amount');
            });
        }

        if (!Schema::hasColumn('reading_error_receipt', 'institution'))
        {
            Schema::table('reading_error_receipt', function (Blueprint $table)
            {
                $table->string('institution')->nullable(false)->after('amount');
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
        Schema::dropIfExists('reading_error_receipt');
    }
};
