<?php

namespace App\Domain\Onboarding\Interfaces;

use App\Domain\Onboarding\DataTransferObjects\OnboardingStatusData;

interface OnboardingRepositoryInterface
{
    public function getOnboardingStatus(): OnboardingStatusData;

    public function countActiveMembers(): int;

    public function countGroupsByDivisionSlug(string $slug): int;

    public function countActiveReviewers(): int;

    public function countUsersWithRole(string $role): int;

    public function hasFinancialSettings(): bool;

    public function getFinancialSettingsValue(): float;

    public function countActiveAccounts(): int;

    public function countActiveCards(): int;
}
