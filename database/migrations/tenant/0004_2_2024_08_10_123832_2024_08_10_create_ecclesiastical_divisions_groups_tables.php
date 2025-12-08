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
        if (!Schema::hasTable('ecclesiastical_divisions_groups'))
        {
            Schema::create('ecclesiastical_divisions_groups', function (Blueprint $table) {
                $table->integer('id', true)->autoIncrement()->comment('ID do grupo');
                $table->integer('ecclesiastical_division_id')->nullable()->comment('FK ecclesiastical_divisions.id');
                $table->integer('parent_group_id')->nullable()->comment('FK para grupo pai (hierarquia)');
                $table->integer('leader_id')->nullable()->comment('FK members.id - líder do grupo');
                $table->string('name')->nullable(false)->comment('Nome do grupo ou ministério');
                $table->string('description')->nullable()->comment('Descrição do grupo');
                $table->boolean('financial_transactions_exists')->nullable(false)->default(0);
                $table->boolean('enabled')->nullable(false)->default(1)->comment('1=grupo ativo, 0=grupo inativo');
                $table->boolean('temporary_event')->nullable();
                $table->boolean('return_values')->nullable()->default(0);
                $table->boolean('return_receiving')->default(0);
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();

                // Relationships
                $table->foreign('ecclesiastical_division_id', 'fk_edg_division')
                    ->references('id')
                    ->on('ecclesiastical_divisions');

                $table->foreign('parent_group_id')
                    ->references('id')
                    ->on('ecclesiastical_divisions_groups');

                $table->timestamps();
            });

            // Adiciona comentário na tabela
            \DB::statement("ALTER TABLE ecclesiastical_divisions_groups COMMENT 'Grupos e ministérios da igreja'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ecclesiastical_divisions_groups');
    }
};
