<?php

declare(strict_types=1);

namespace Application\Core\Http\Policies;

use App\Domain\Accounts\Users\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupPolicy
{
    use HandlesAuthorization;

    /**
     * Roles que podem visualizar grupos.
     */
    private const VIEW_ROLES = ['admin', 'pastor', 'treasury', 'secretary', 'patrimony'];

    /**
     * Roles que podem gerenciar grupos.
     */
    private const MANAGEMENT_ROLES = ['admin', 'pastor'];

    /**
     * Roles que podem gerenciar equipes/membros de grupos.
     */
    private const TEAM_ROLES = ['admin', 'pastor', 'secretary'];

    /**
     * Roles que podem ver movimentações de grupos.
     */
    private const MOVEMENT_ROLES = ['admin', 'pastor', 'treasury'];

    /**
     * Determine whether the user can view any groups.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(self::VIEW_ROLES);
    }

    /**
     * Determine whether the user can view the group.
     */
    public function view(User $user): bool
    {
        return $user->hasAnyRole(self::VIEW_ROLES);
    }

    /**
     * Determine whether the user can create groups.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(self::MANAGEMENT_ROLES);
    }

    /**
     * Determine whether the user can update the group.
     */
    public function update(User $user): bool
    {
        return $user->hasAnyRole(self::MANAGEMENT_ROLES);
    }

    /**
     * Determine whether the user can delete the group.
     */
    public function delete(User $user): bool
    {
        return $user->hasAnyRole(self::MANAGEMENT_ROLES);
    }

    /**
     * Determine whether the user can view group members.
     */
    public function viewMembers(User $user): bool
    {
        return $user->hasAnyRole(self::TEAM_ROLES);
    }

    /**
     * Determine whether the user can add members to group.
     */
    public function addMembers(User $user): bool
    {
        return $user->hasAnyRole(self::TEAM_ROLES);
    }

    /**
     * Determine whether the user can view group movements.
     */
    public function viewMovements(User $user): bool
    {
        return $user->hasAnyRole(self::MOVEMENT_ROLES);
    }

    /**
     * Determine whether the user can export group movements data.
     */
    public function exportMovements(User $user): bool
    {
        return $user->hasAnyRole(self::MOVEMENT_ROLES);
    }
}
