<?php

declare(strict_types=1);

namespace Application\Core\Http\Policies;

use App\Domain\Accounts\Users\Models\User;
use App\Domain\Financial\Reviewers\Models\FinancialReviewer;
use Illuminate\Auth\Access\HandlesAuthorization;

class FinancialReviewerPolicy
{
    use HandlesAuthorization;

    /**
     * Roles que podem visualizar revisores financeiros.
     */
    private const VIEW_ROLES = ['admin', 'pastor', 'treasury'];

    /**
     * Roles que podem gerenciar revisores financeiros.
     */
    private const MANAGEMENT_ROLES = ['admin', 'pastor'];

    /**
     * Determine whether the user can view any financial reviewers.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(self::VIEW_ROLES);
    }

    /**
     * Determine whether the user can view the financial reviewer.
     */
    public function view(User $user, FinancialReviewer $reviewer): bool
    {
        return $user->hasAnyRole(self::VIEW_ROLES);
    }

    /**
     * Determine whether the user can create financial reviewers.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(self::MANAGEMENT_ROLES);
    }

    /**
     * Determine whether the user can batch create financial reviewers.
     */
    public function batchCreate(User $user): bool
    {
        return $user->hasAnyRole(self::MANAGEMENT_ROLES);
    }

    /**
     * Determine whether the user can update the financial reviewer.
     */
    public function update(User $user, FinancialReviewer $reviewer): bool
    {
        return $user->hasAnyRole(self::MANAGEMENT_ROLES);
    }

    /**
     * Determine whether the user can delete the financial reviewer.
     */
    public function delete(User $user, FinancialReviewer $reviewer): bool
    {
        return $user->hasAnyRole(self::MANAGEMENT_ROLES);
    }
}
