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
        if (!Schema::hasTable('ecclesiastical_divisions_groups_balances'))
        {
            Schema::create('ecclesiastical_divisions_groups_balances', function (Blueprint $table) {

                $table->integer('id', true)->autoIncrement();
                $table->integer('ecclesiastical_divisions_groups_id')->nullable(false);
                $table->integer('entry_id')->nullable(false);
                $table->integer('exit_id')->nullable(false);

                $table->foreign('entry_id')
                    ->references('id')
                    ->on('entries');

                $table->foreign('exit_id')
                    ->references('id')
                    ->on('exits');

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
        Schema::dropIfExists('ecclesiastical_divisions_groups_balances');
    }
};
