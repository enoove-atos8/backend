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
        if (!Schema::hasTable('members'))
        {
            Schema::create('members', function (Blueprint $table) {
                $table->integer('id', true)->autoIncrement()->comment('ID do membro');

                // Relationships with ecclesiastical divisions (ministry, department, organization, event)
                $table->integer('ecclesiastical_divisions_group_id')->nullable();
                $table->boolean('group_leader')->nullable()->default(0);

                // Status of the member
                $table->boolean('activated')->default(0)->comment('1=membro ativo, 0=membro inativo');
                $table->boolean('deleted')->default(0)->comment('1=registro deletado (soft delete)');

                // Profile information
                $table->string('avatar')->nullable();
                $table->string('full_name')->nullable(false)->comment('Nome completo');
                $table->string('gender')->nullable(false)->comment('Gênero: masculino ou feminino');
                $table->string('cpf')->nullable()->unique()->comment('CPF do membro');
                $table->string('rg')->nullable()->unique()->comment('RG do membro');
                $table->string('work')->nullable()->comment('Profissão');

                // Personal and contact details
                $table->string('born_date')->nullable(false)->comment('Data de nascimento formato YYYY-MM-DD');
                $table->string('email')->nullable()->comment('E-mail');
                $table->string('phone')->nullable()->comment('Telefone fixo');
                $table->string('cell_phone')->nullable(false)->comment('Celular');
                $table->string('address')->nullable(false)->comment('Endereço completo');
                $table->string('district')->nullable(false)->comment('Bairro');
                $table->string('city')->nullable(false)->comment('Cidade');
                $table->string('uf')->nullable(false)->comment('Estado (UF)');

                // Family and additional information
                $table->string('marital_status')->nullable()->comment('Estado civil: solteiro, casado, viuvo, divorciado');
                $table->string('spouse')->nullable()->comment('Nome do cônjuge');
                $table->string('father')->nullable()->comment('Nome do pai');
                $table->string('mother')->nullable(false)->comment('Nome da mãe');
                $table->string('ecclesiastical_function')->nullable()->comment('Função eclesiástica: pastor, diacono, presbitero, evangelista, missionario, obreiro');

                // Member type and additional information
                $table->string('member_type')->nullable(false)->comment('Tipo de membro: membro, congregado, visitante');
                $table->string('baptism_date')->nullable()->comment('Data do batismo formato YYYY-MM-DD');
                $table->string('blood_type')->nullable()->comment('Tipo sanguíneo');
                $table->string('education')->nullable()->comment('Escolaridade');

                // Remember token for authentication
                $table->rememberToken();

                // Date and time control fields
                $table->timestamps();

                // Relationships with ecclesiastical divisions areas
                $table->foreign('ecclesiastical_divisions_group_id')
                    ->references('id')
                    ->on('ecclesiastical_divisions_groups')
                    ->onDelete('set null');
            });

            // Adiciona comentário na tabela
            \DB::statement("ALTER TABLE members COMMENT 'Membros da igreja'");
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
