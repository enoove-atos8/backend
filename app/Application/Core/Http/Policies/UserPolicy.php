<?php

declare(strict_types=1);

namespace Application\Core\Http\Policies;

use App\Domain\Accounts\Users\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Roles que podem gerenciar usuários.
     */
    private const MANAGEMENT_ROLES = ['admin', 'pastor'];

    /**
     * Determine whether the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(self::MANAGEMENT_ROLES);
    }

    /**
     * Determine whether the user can view the target user.
     */
    public function view(User $user, User $targetUser): bool
    {
        // Usuário pode ver seu próprio perfil ou se tiver role de gerenciamento
        return $user->id === $targetUser->id || $user->hasAnyRole(self::MANAGEMENT_ROLES);
    }

    /**
     * Determine whether the user can create users.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(self::MANAGEMENT_ROLES);
    }

    /**
     * Determine whether the user can update the target user.
     */
    public function update(User $user, User $targetUser): bool
    {
        // Usuário pode atualizar seu próprio perfil ou se tiver role de gerenciamento
        return $user->id === $targetUser->id || $user->hasAnyRole(self::MANAGEMENT_ROLES);
    }

    /**
     * Determine whether the user can update status of the target user.
     */
    public function updateStatus(User $user, User $targetUser): bool
    {
        // Não pode alterar seu próprio status
        if ($user->id === $targetUser->id) {
            return false;
        }

        return $user->hasAnyRole(self::MANAGEMENT_ROLES);
    }

    /**
     * Determine whether the user can delete the target user.
     */
    public function delete(User $user, User $targetUser): bool
    {
        // Não pode deletar a si mesmo e apenas admin pode deletar
        if ($user->id === $targetUser->id) {
            return false;
        }

        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can change their own password.
     */
    public function changePassword(User $user, User $targetUser): bool
    {
        // Usuário só pode alterar sua própria senha
        return $user->id === $targetUser->id;
    }

    /**
     * Determine whether the user can upload avatar.
     */
    public function uploadAvatar(User $user, User $targetUser): bool
    {
        return $user->id === $targetUser->id || $user->hasAnyRole(self::MANAGEMENT_ROLES);
    }
}
