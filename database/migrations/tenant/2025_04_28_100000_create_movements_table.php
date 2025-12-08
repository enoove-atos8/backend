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

                $table->id()->comment('ID da movimentação');
                $table->integer('group_id')->comment('FK ecclesiastical_divisions_groups.id');
                $table->integer('entry_id')->nullable()->comment('FK entries.id - entrada relacionada');
                $table->integer('exit_id')->nullable()->comment('FK exits.id - saída relacionada');
                $table->enum('type', ['entry', 'exit'])->nullable()->comment('Tipo: entry=entrada, exit=saída');
                $table->string('sub_type')->nullable()->comment('Subtipo da movimentação');
                $table->decimal('amount', 10)->comment('Valor da movimentação em reais');
                $table->decimal('balance', 10)->nullable()->comment('Saldo do grupo após a movimentação');
                $table->string('description')->nullable();
                $table->string('movement_date')->nullable(false)->comment('Data da movimentação formato YYYY-MM-DD');
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

            // Adiciona comentário na tabela
            \DB::statement("ALTER TABLE movements COMMENT 'Movimentações financeiras dos grupos'");
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
