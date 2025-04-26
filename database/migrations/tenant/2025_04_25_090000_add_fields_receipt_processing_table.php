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
        if (!Schema::hasColumn('receipt_processing', 'reviewer_id'))
        {
            Schema::table('receipt_processing', function (Blueprint $table)
            {
                $table->integer('reviewer_id')->nullable(false)->after('doc_sub_type');

                $table->foreign('reviewer_id')
                    ->references('id')
                    ->on('financial_reviewers');
            });
        }

        if (!Schema::hasColumn('receipt_processing', 'transaction_type'))
        {
            Schema::table('receipt_processing', function (Blueprint $table)
            {
                $table->string('transaction_type')->nullable(false)->after('deleted');
            });
        }

        if (!Schema::hasColumn('receipt_processing', 'transaction_compensation'))
        {
            Schema::table('receipt_processing', function (Blueprint $table)
            {
                $table->string('transaction_compensation')->nullable(false)->after('transaction_type');
            });
        }

        if (!Schema::hasColumn('receipt_processing', 'date_transaction_compensation'))
        {
            Schema::table('receipt_processing', function (Blueprint $table)
            {
                $table->string('date_transaction_compensation')->nullable()->after('transaction_compensation');
            });
        }

        if (!Schema::hasColumn('receipt_processing', 'date_register'))
        {
            Schema::table('receipt_processing', function (Blueprint $table)
            {
                $table->string('date_register')->nullable(false)->after('date_transaction_compensation');
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
        Schema::dropIfExists('receipt_processing');
    }
};
