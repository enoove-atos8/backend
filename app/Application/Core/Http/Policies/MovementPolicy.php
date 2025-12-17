<?php

declare(strict_types=1);

namespace Application\Core\Http\Policies;

use App\Domain\Accounts\Users\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MovementPolicy
{
    use HandlesAuthorization;

    /**
     * Roles que podem visualizar movimentações.
     */
    private const VIEW_ROLES = ['admin', 'pastor', 'treasury'];

    /**
     * Roles que podem gerenciar movimentações (saldo inicial, reset).
     */
    private const MANAGEMENT_ROLES = ['admin', 'pastor'];

    /**
     * Determine whether the user can view movements by group.
     */
    public function viewByGroup(User $user): bool
    {
        return $user->hasAnyRole(self::VIEW_ROLES);
    }

    /**
     * Determine whether the user can view indicators by group.
     */
    public function viewIndicators(User $user): bool
    {
        return $user->hasAnyRole(self::VIEW_ROLES);
    }

    /**
     * Determine whether the user can add initial balance.
     */
    public function addInitialBalance(User $user): bool
    {
        return $user->hasAnyRole(self::MANAGEMENT_ROLES);
    }

    /**
     * Determine whether the user can reset balance.
     */
    public function resetBalance(User $user): bool
    {
        return $user->hasAnyRole(self::MANAGEMENT_ROLES);
    }
}
