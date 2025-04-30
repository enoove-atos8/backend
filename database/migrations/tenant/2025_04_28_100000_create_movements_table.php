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
    public function up()
    {
        if (!Schema::hasTable('movements'))
        {
            Schema::create('movements', function (Blueprint $table) {

                $table->id();
                $table->integer('group_id');
                $table->integer('entry_id')->nullable();
                $table->integer('exit_id')->nullable();
                $table->enum('type', ['entry', 'exit'])->nullable();
                $table->string('sub_type')->nullable();
                $table->decimal('amount', 10);
                $table->decimal('balance', 10);
                $table->string('description')->nullable();
                $table->date('movement_date');
                $table->boolean('is_initial_balance')->default(false);
                $table->timestamps();

                $table->foreign('group_id')
                    ->references('id')
                    ->on('ecclesiastical_divisions_groups')
                    ->onDelete('cascade');

                $table->foreign('entry_id')
                    ->references('id')
                    ->on('entries')
                    ->onDelete('set null');

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
    public function down()
    {
        Schema::dropIfExists('movements');
    }
};
