<?php

declare(strict_types=1);

namespace Application\Core\Http\Policies;

use App\Domain\Accounts\Users\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConsolidationPolicy
{
    use HandlesAuthorization;

    /**
     * Roles que podem visualizar consolidações.
     */
    private const VIEW_ROLES = ['admin', 'pastor', 'treasury'];

    /**
     * Roles que podem consolidar/reabrir meses.
     */
    private const MANAGEMENT_ROLES = ['admin', 'pastor'];

    /**
     * Determine whether the user can view consolidation months.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(self::VIEW_ROLES);
    }

    /**
     * Determine whether the user can view total amount entries.
     */
    public function viewTotalAmount(User $user): bool
    {
        return $user->hasAnyRole(self::VIEW_ROLES);
    }

    /**
     * Determine whether the user can consolidate a month.
     */
    public function consolidate(User $user): bool
    {
        return $user->hasAnyRole(self::MANAGEMENT_ROLES);
    }

    /**
     * Determine whether the user can reopen a consolidated month.
     */
    public function reopen(User $user): bool
    {
        return $user->hasAnyRole(self::MANAGEMENT_ROLES);
    }
}
