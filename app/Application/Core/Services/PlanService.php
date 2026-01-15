<?php

declare(strict_types=1);

namespace Application\Core\Services;

use Application\Core\Enums\PlanType;
use Domain\CentralDomain\Churches\Church\Models\Church;
use Domain\CentralDomain\Plans\Models\Plan;
use Domain\Secretary\Membership\Actions\CountActiveMembersAction;
use Illuminate\Support\Facades\DB;

class PlanService
{
    public function __construct(
        private CountActiveMembersAction $countActiveMembersAction
    ) {}

    /**
     * Obtém o plano do tenant atual.
     */
    public function getCurrentPlan(): ?PlanType
    {
        $tenantId = tenant('id');

        if (! $tenantId) {
            return null;
        }

        $church = $this->getChurchByTenantId($tenantId);

        if (! $church) {
            return null;
        }

        return PlanType::fromId($church->plan_id);
    }

    /**
     * Obtém os dados do plano do tenant atual.
     */
    public function getCurrentPlanData(): ?array
    {
        $tenantId = tenant('id');

        if (! $tenantId) {
            return null;
        }

        $church = $this->getChurchByTenantId($tenantId);

        if (! $church) {
            return null;
        }

        $plan = $this->getPlanById($church->plan_id);

        if (! $plan) {
            return null;
        }

        $planData = [
            'plan_id' => $plan->id,
            'plan_name' => $plan->name,
            'plan_type' => PlanType::fromId($plan->id),
            'features' => $plan->features,
            'members_limit' => $plan->features['members_limit'] ?? null,
        ];

        // Contagem de membros sempre em tempo real (sem cache)
        $planData['member_count'] = $this->countCurrentMembers();

        return $planData;
    }

    /**
     * Conta os membros ativos do tenant atual.
     */
    public function countCurrentMembers(): int
    {
        return $this->countActiveMembersAction->execute();
    }

    /**
     * Verifica se o tenant atual tem um plano igual ou superior ao informado.
     */
    public function hasAtLeastPlan(PlanType $requiredPlan): bool
    {
        $currentPlan = $this->getCurrentPlan();

        if (! $currentPlan) {
            return false;
        }

        return $currentPlan->isAtLeast($requiredPlan);
    }

    /**
     * Verifica se o tenant pode adicionar mais membros.
     */
    public function canAddMembers(int $quantity = 1): bool
    {
        $planData = $this->getCurrentPlanData();

        if (! $planData) {
            return false;
        }

        $membersLimit = $planData['members_limit'];

        // Plano sem limite (diamond)
        if ($membersLimit === null) {
            return true;
        }

        $currentCount = $planData['member_count'] ?? 0;

        return ($currentCount + $quantity) <= $membersLimit;
    }

    /**
     * Retorna quantos membros ainda podem ser adicionados.
     */
    public function getRemainingMembersSlots(): ?int
    {
        $planData = $this->getCurrentPlanData();

        if (! $planData) {
            return 0;
        }

        $membersLimit = $planData['members_limit'];

        // Plano sem limite (diamond)
        if ($membersLimit === null) {
            return null; // ilimitado
        }

        $currentCount = $planData['member_count'] ?? 0;

        return max(0, $membersLimit - $currentCount);
    }

    /**
     * Verifica se o tenant tem acesso a uma feature específica.
     */
    public function hasFeature(string $featureName): bool
    {
        $planData = $this->getCurrentPlanData();

        if (! $planData || ! isset($planData['features'])) {
            return false;
        }

        return $planData['features'][$featureName] ?? false;
    }

    /**
     * Verifica se o tenant tem acesso a relatórios avançados.
     */
    public function hasAdvancedReports(): bool
    {
        $currentPlan = $this->getCurrentPlan();

        return $currentPlan?->hasAdvancedReports() ?? false;
    }

    /**
     * Verifica se o tenant tem suporte prioritário.
     */
    public function hasPrioritySupport(): bool
    {
        $currentPlan = $this->getCurrentPlan();

        return $currentPlan?->hasPrioritySupport() ?? false;
    }

    /**
     * Verifica se o tenant tem acesso ao repositório de comprovantes em nuvem.
     */
    public function hasCloudReceiptsRepository(): bool
    {
        $tenantId = tenant('id');

        if (! $tenantId) {
            return false;
        }

        $church = $this->getChurchByTenantId($tenantId);

        if (! $church) {
            return false;
        }

        // Verifica na tabela functionalities se a feature está disponível para o plano
        return DB::connection('mysql')
            ->table('functionalities')
            ->where('plan_id', '<=', $church->plan_id)
            ->where('name', 'cloud_repository_receipts')
            ->where('activated', true)
            ->exists();
    }

    /**
     * Limpa o cache do plano do tenant atual.
     * Nota: Cache foi removido pois não faz sentido para o padrão de uso (cadastro em massa único + esporádico)
     */
    public function clearCache(): void
    {
        // Método mantido apenas para compatibilidade com código existente
    }

    /**
     * Obtém a igreja pelo tenant ID (usando conexão central).
     */
    private function getChurchByTenantId(string $tenantId): ?Church
    {
        return Church::on('mysql')
            ->where('tenant_id', $tenantId)
            ->first();
    }

    /**
     * Obtém o plano pelo ID (usando conexão central).
     */
    private function getPlanById(int $planId): ?Plan
    {
        return Plan::on('mysql')
            ->where('id', $planId)
            ->first();
    }
}
