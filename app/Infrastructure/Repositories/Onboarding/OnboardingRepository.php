<?php

namespace App\Infrastructure\Repositories\Onboarding;

use App\Domain\Onboarding\DataTransferObjects\OnboardingStatusData;
use App\Domain\Onboarding\DataTransferObjects\OnboardingStepData;
use App\Domain\Onboarding\Interfaces\OnboardingRepositoryInterface;
use Illuminate\Support\Facades\DB;

class OnboardingRepository implements OnboardingRepositoryInterface
{
    private const STEP_MEMBERS = 1;

    private const STEP_GROUPS = 2;

    private const STEP_REVIEWER = 3;

    private const STEP_USER_ROLES = 4;

    private const STEP_BUDGET = 5;

    private const STEP_ACCOUNTS = 6;

    private const STEP_CARDS = 7;

    private const TOTAL_REQUIRED_STEPS = 6;

    private const TOTAL_STEPS = 7;

    // Tabelas
    private const MEMBERS_TABLE = 'members';

    private const GROUPS_TABLE = 'ecclesiastical_divisions_groups';

    private const DIVISIONS_TABLE = 'ecclesiastical_divisions';

    private const REVIEWERS_TABLE = 'financial_reviewers';

    private const USERS_TABLE = 'users';

    private const MODEL_HAS_ROLES_TABLE = 'model_has_roles';

    private const ROLES_TABLE = 'roles';

    private const FINANCIAL_SETTINGS_TABLE = 'financial_settings';

    private const ACCOUNTS_TABLE = 'accounts';

    private const CARDS_TABLE = 'cards';

    public function getOnboardingStatus(): OnboardingStatusData
    {
        $steps = $this->checkAllSteps();
        $pendingSteps = $this->getPendingSteps($steps);
        $currentStep = $this->getCurrentStep($steps);
        $completedRequiredSteps = $this->countCompletedRequiredSteps($steps);

        return OnboardingStatusData::fromResponse(
            completed: $completedRequiredSteps >= self::TOTAL_REQUIRED_STEPS,
            currentStep: $currentStep,
            totalSteps: self::TOTAL_STEPS,
            totalRequiredSteps: self::TOTAL_REQUIRED_STEPS,
            completedSteps: $completedRequiredSteps,
            progressPercentage: (int) round(($completedRequiredSteps / self::TOTAL_REQUIRED_STEPS) * 100),
            steps: $steps,
            pendingSteps: $pendingSteps,
        );
    }

    public function countActiveMembers(): int
    {
        return DB::table(self::MEMBERS_TABLE)
            ->where('deleted', 0)
            ->count();
    }

    public function countGroupsByDivisionSlug(string $slug): int
    {
        $divisionName = $this->getDivisionNameBySlug($slug);

        return DB::table(self::GROUPS_TABLE)
            ->join(
                self::DIVISIONS_TABLE,
                self::GROUPS_TABLE.'.ecclesiastical_division_id',
                '=',
                self::DIVISIONS_TABLE.'.id'
            )
            ->where(self::DIVISIONS_TABLE.'.name', $divisionName)
            ->where(self::GROUPS_TABLE.'.enabled', 1)
            ->count();
    }

    private function getDivisionNameBySlug(string $slug): string
    {
        $map = [
            'ministries' => 'Ministérios',
            'departures' => 'Departamentos',
            'organizations' => 'Organizações',
        ];

        return $map[$slug] ?? $slug;
    }

    public function countActiveReviewers(): int
    {
        return DB::table(self::REVIEWERS_TABLE)
            ->where('activated', 1)
            ->where('deleted', 0)
            ->count();
    }

    public function countUsersWithRole(string $role): int
    {
        return DB::table(self::USERS_TABLE)
            ->join(
                self::MODEL_HAS_ROLES_TABLE,
                self::USERS_TABLE.'.id',
                '=',
                self::MODEL_HAS_ROLES_TABLE.'.model_id'
            )
            ->join(
                self::ROLES_TABLE,
                self::MODEL_HAS_ROLES_TABLE.'.role_id',
                '=',
                self::ROLES_TABLE.'.id'
            )
            ->where(self::ROLES_TABLE.'.name', $role)
            ->where(self::USERS_TABLE.'.activated', 1)
            ->count();
    }

    public function hasFinancialSettings(): bool
    {
        $settings = DB::table(self::FINANCIAL_SETTINGS_TABLE)->first();

        return $settings !== null && $settings->monthly_budget_tithes > 0;
    }

    public function getFinancialSettingsValue(): float
    {
        $settings = DB::table(self::FINANCIAL_SETTINGS_TABLE)->first();

        return $settings?->monthly_budget_tithes ?? 0;
    }

    public function countActiveAccounts(): int
    {
        return DB::table(self::ACCOUNTS_TABLE)
            ->where('activated', 1)
            ->count();
    }

    public function countActiveCards(): int
    {
        return DB::table(self::CARDS_TABLE)
            ->where('active', 1)
            ->where('deleted', 0)
            ->count();
    }

    /**
     * @return OnboardingStepData[]
     */
    private function checkAllSteps(): array
    {
        return [
            $this->checkMembersStep(),
            $this->checkGroupsStep(),
            $this->checkReviewerStep(),
            $this->checkUserRolesStep(),
            $this->checkBudgetStep(),
            $this->checkAccountsStep(),
            $this->checkCardsStep(),
        ];
    }

    private function checkMembersStep(): OnboardingStepData
    {
        $count = $this->countActiveMembers();

        return OnboardingStepData::fromResponse([
            'step' => self::STEP_MEMBERS,
            'name' => 'members',
            'title' => 'Membresia',
            'description' => 'Cadastre os membros da igreja',
            'completed' => $count > 0,
            'required' => true,
            'count' => $count,
            'minimum_required' => 1,
        ]);
    }

    private function checkGroupsStep(): OnboardingStepData
    {
        $ministriesCount = $this->countGroupsByDivisionSlug('ministries');
        $departmentsCount = $this->countGroupsByDivisionSlug('departures');
        $organizationsCount = $this->countGroupsByDivisionSlug('organizations');

        $totalCount = $ministriesCount + $departmentsCount + $organizationsCount;
        $hasRequiredGroups = $ministriesCount > 0 && $departmentsCount > 0 && $organizationsCount > 0;

        return OnboardingStepData::fromResponse([
            'step' => self::STEP_GROUPS,
            'name' => 'groups',
            'title' => 'Grupos Eclesiásticos',
            'description' => 'Cadastre os grupos de Ministérios, Departamentos e Organizações',
            'completed' => $hasRequiredGroups,
            'required' => true,
            'count' => $totalCount,
            'details' => [
                'ministries' => $ministriesCount,
                'departments' => $departmentsCount,
                'organizations' => $organizationsCount,
            ],
        ]);
    }

    private function checkReviewerStep(): OnboardingStepData
    {
        $count = $this->countActiveReviewers();

        return OnboardingStepData::fromResponse([
            'step' => self::STEP_REVIEWER,
            'name' => 'financial_reviewer',
            'title' => 'Revisor Financeiro',
            'description' => 'Cadastre pelo menos um revisor financeiro',
            'completed' => $count > 0,
            'required' => true,
            'count' => $count,
            'minimum_required' => 1,
        ]);
    }

    private function checkUserRolesStep(): OnboardingStepData
    {
        $hasTreasury = $this->countUsersWithRole('treasury') > 0;
        $hasPastor = $this->countUsersWithRole('pastor') > 0;
        $hasSecretary = $this->countUsersWithRole('secretary') > 0;
        $hasPatrimony = $this->countUsersWithRole('patrimony') > 0;

        $hasMinimumRoles = $hasTreasury && $hasPastor;
        $count = ($hasTreasury ? 1 : 0) + ($hasPastor ? 1 : 0) + ($hasSecretary ? 1 : 0) + ($hasPatrimony ? 1 : 0);

        return OnboardingStepData::fromResponse([
            'step' => self::STEP_USER_ROLES,
            'name' => 'user_roles',
            'title' => 'Vínculo de Perfis',
            'description' => 'Defina os responsáveis: Tesoureiro, Pastor, Secretaria e Patrimônio',
            'completed' => $hasMinimumRoles,
            'required' => true,
            'count' => $count,
            'minimum_required' => 2,
            'details' => [
                'treasury' => $hasTreasury,
                'pastor' => $hasPastor,
                'secretary' => $hasSecretary,
                'patrimony' => $hasPatrimony,
            ],
        ]);
    }

    private function checkBudgetStep(): OnboardingStepData
    {
        $hasSettings = $this->hasFinancialSettings();
        $currentValue = $this->getFinancialSettingsValue();

        return OnboardingStepData::fromResponse([
            'step' => self::STEP_BUDGET,
            'name' => 'budget',
            'title' => 'Orçamento Mensal',
            'description' => 'Defina a meta de arrecadação mensal de dízimos',
            'completed' => $hasSettings,
            'required' => true,
            'count' => $hasSettings ? 1 : 0,
            'details' => [
                'current_value' => $currentValue,
            ],
        ]);
    }

    private function checkAccountsStep(): OnboardingStepData
    {
        $count = $this->countActiveAccounts();

        return OnboardingStepData::fromResponse([
            'step' => self::STEP_ACCOUNTS,
            'name' => 'accounts',
            'title' => 'Contas Bancárias',
            'description' => 'Cadastre pelo menos uma conta bancária',
            'completed' => $count > 0,
            'required' => true,
            'count' => $count,
            'minimum_required' => 1,
        ]);
    }

    private function checkCardsStep(): OnboardingStepData
    {
        $count = $this->countActiveCards();

        return OnboardingStepData::fromResponse([
            'step' => self::STEP_CARDS,
            'name' => 'cards',
            'title' => 'Cartões de Crédito',
            'description' => 'Cadastre os cartões de crédito da igreja (opcional)',
            'completed' => $count > 0,
            'required' => false,
            'count' => $count,
            'skippable' => true,
        ]);
    }

    /**
     * @param  OnboardingStepData[]  $steps
     * @return OnboardingStepData[]
     */
    private function getPendingSteps(array $steps): array
    {
        $pending = [];

        foreach ($steps as $step) {
            if (! $step->completed && $step->required) {
                $pending[] = $step;
            }
        }

        return $pending;
    }

    /**
     * @param  OnboardingStepData[]  $steps
     */
    private function getCurrentStep(array $steps): int
    {
        foreach ($steps as $step) {
            if (! $step->completed && $step->required) {
                return $step->step;
            }
        }

        return self::TOTAL_STEPS;
    }

    /**
     * @param  OnboardingStepData[]  $steps
     */
    private function countCompletedRequiredSteps(array $steps): int
    {
        $count = 0;

        foreach ($steps as $step) {
            if ($step->completed && $step->required) {
                $count++;
            }
        }

        return $count;
    }
}
