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
        if (!Schema::hasTable('ecclesiastical_divisions_groups_transactions'))
        {
            Schema::create('ecclesiastical_divisions_groups_transactions', function (Blueprint $table) {

                $table->integer('id', true)->autoIncrement();
                $table->integer('ecclesiastical_divisions_groups_id')->nullable(false);
                $table->integer('entry_id')->nullable(false);
                $table->integer('exit_id')->nullable(false);
                $table->string('date_time_transaction')->nullable(false);
                $table->string('transaction_type')->nullable(false);
                $table->decimal('balance')->nullable(false);
                $table->decimal('amount')->nullable(false);
                $table->boolean('deleted')->default(0);

                $table->timestamps();

                $table->foreign('ecclesiastical_divisions_groups_id', 'fk_edg_groups')
                    ->references('id')
                    ->on('ecclesiastical_divisions_groups');

                $table->foreign('entry_id')
                    ->references('id')
                    ->on('entries')
                    ->onDelete('set null');

                $table->foreign('exit_id')
                    ->references('id')
                    ->on('exits')
                    ->onDelete('set null');

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
        Schema::dropIfExists('ecclesiastical_divisions_groups_transactions');
    }
};
