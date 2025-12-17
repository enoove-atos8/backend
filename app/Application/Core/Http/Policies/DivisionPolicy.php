<?php

declare(strict_types=1);

namespace Application\Core\Http\Policies;

use App\Domain\Accounts\Users\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DivisionPolicy
{
    use HandlesAuthorization;

    /**
     * Roles que podem visualizar divisões.
     */
    private const VIEW_ROLES = ['admin', 'pastor', 'treasury', 'secretary', 'patrimony'];

    /**
     * Roles que podem gerenciar divisões.
     */
    private const MANAGEMENT_ROLES = ['admin', 'pastor'];

    /**
     * Determine whether the user can view any divisions.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(self::VIEW_ROLES);
    }

    /**
     * Determine whether the user can view the division.
     */
    public function view(User $user): bool
    {
        return $user->hasAnyRole(self::VIEW_ROLES);
    }

    /**
     * Determine whether the user can create divisions.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(self::MANAGEMENT_ROLES);
    }

    /**
     * Determine whether the user can update the division.
     */
    public function update(User $user): bool
    {
        return $user->hasAnyRole(self::MANAGEMENT_ROLES);
    }

    /**
     * Determine whether the user can delete the division.
     */
    public function delete(User $user): bool
    {
        return $user->hasAnyRole(self::MANAGEMENT_ROLES);
    }
}
