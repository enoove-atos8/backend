<?php

declare(strict_types=1);

namespace Application\Core\Http\Policies;

use App\Domain\Accounts\Users\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExitPolicy
{
    use HandlesAuthorization;

    /**
     * Roles que têm acesso ao módulo de saídas.
     */
    private const ALLOWED_ROLES = ['admin', 'pastor', 'treasury'];

    /**
     * Determine whether the user can view any exits.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can view the exit.
     */
    public function view(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can create exits.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can update the exit.
     */
    public function update(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can delete the exit.
     */
    public function delete(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can view indicators.
     */
    public function viewIndicators(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }
}
