<?php

use Illuminate\Database\Migrations\Migration;

/**
 * Migration de compatibilidade para tenants antigos.
 *
 * Os comentários de tabelas e colunas agora são adicionados diretamente
 * nas migrations originais de criação de tabelas, garantindo que novos
 * tenants já recebam os comentários na criação.
 *
 * Esta migration existe apenas para manter compatibilidade com tenants
 * que foram criados antes dessa mudança.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Comentários agora são adicionados nas migrations originais.
        // Esta migration existe apenas para manter compatibilidade
        // com tenants que já executaram esta migration anteriormente.
    }

    public function down(): void
    {
        // Nada a reverter - comentários não afetam a estrutura do banco
    }
};
