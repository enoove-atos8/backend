<?php

declare(strict_types=1);

namespace Application\Core\Http\Policies;

use App\Domain\Accounts\Users\Models\User;
use Application\Core\Enums\PlanType;
use Application\Core\Services\PlanService;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlanPolicy
{
    use HandlesAuthorization;

    public function __construct(
        private PlanService $planService
    ) {}

    /**
     * Verifica se o tenant pode adicionar novos membros.
     */
    public function addMember(User $user, int $quantity = 1): bool
    {
        return $this->planService->canAddMembers($quantity);
    }

    /**
     * Verifica se o tenant pode adicionar membros em lote.
     */
    public function batchAddMembers(User $user, int $quantity): bool
    {
        return $this->planService->canAddMembers($quantity);
    }

    /**
     * Verifica se o tenant tem acesso a relatórios avançados.
     */
    public function accessAdvancedReports(User $user): bool
    {
        return $this->planService->hasAdvancedReports();
    }

    /**
     * Verifica se o tenant tem acesso ao suporte prioritário.
     */
    public function accessPrioritySupport(User $user): bool
    {
        return $this->planService->hasPrioritySupport();
    }

    /**
     * Verifica se o tenant tem acesso ao repositório de comprovantes em nuvem.
     */
    public function accessCloudReceiptsRepository(User $user): bool
    {
        return $this->planService->hasCloudReceiptsRepository();
    }

    /**
     * Verifica se o tenant tem pelo menos o plano Bronze.
     */
    public function hasAtLeastBronze(User $user): bool
    {
        return $this->planService->hasAtLeastPlan(PlanType::Bronze);
    }

    /**
     * Verifica se o tenant tem pelo menos o plano Silver.
     */
    public function hasAtLeastSilver(User $user): bool
    {
        return $this->planService->hasAtLeastPlan(PlanType::Silver);
    }

    /**
     * Verifica se o tenant tem pelo menos o plano Gold.
     */
    public function hasAtLeastGold(User $user): bool
    {
        return $this->planService->hasAtLeastPlan(PlanType::Gold);
    }

    /**
     * Verifica se o tenant tem o plano Diamond.
     */
    public function hasDiamond(User $user): bool
    {
        return $this->planService->hasAtLeastPlan(PlanType::Diamond);
    }

    /**
     * Verifica se o tenant tem acesso a uma feature genérica.
     */
    public function hasFeature(User $user, string $featureName): bool
    {
        return $this->planService->hasFeature($featureName);
    }
}
