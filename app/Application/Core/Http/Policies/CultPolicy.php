<?php

declare(strict_types=1);

namespace Application\Core\Http\Policies;

use App\Domain\Accounts\Users\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CultPolicy
{
    use HandlesAuthorization;

    /**
     * Roles que têm acesso ao módulo de cultos.
     */
    private const ALLOWED_ROLES = ['admin', 'pastor', 'treasury'];

    /**
     * Determine whether the user can view any cults.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can view the cult.
     */
    public function view(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can create cults.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can update the cult.
     */
    public function update(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can delete the cult.
     */
    public function delete(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }
}
