<?php

declare(strict_types=1);

namespace Application\Core\Http\Policies;

use App\Domain\Accounts\Users\Models\User;
use Domain\Financial\AccountsAndCards\Accounts\Models\Accounts;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountPolicy
{
    use HandlesAuthorization;

    /**
     * Roles que têm acesso ao módulo de contas.
     */
    private const ALLOWED_ROLES = ['admin', 'pastor', 'treasury'];

    /**
     * Roles que podem desativar contas.
     */
    private const DEACTIVATE_ROLES = ['admin', 'pastor'];

    /**
     * Determine whether the user can view any accounts.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can view the account.
     */
    public function view(User $user, Accounts $account): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can create accounts.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can update the account.
     */
    public function update(User $user, Accounts $account): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can deactivate the account.
     */
    public function deactivate(User $user, Accounts $account): bool
    {
        return $user->hasAnyRole(self::DEACTIVATE_ROLES);
    }

    /**
     * Determine whether the user can delete the account.
     */
    public function delete(User $user, Accounts $account): bool
    {
        // Apenas admin pode deletar permanentemente
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view account files.
     */
    public function viewFiles(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can upload files.
     */
    public function uploadFile(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can process files.
     */
    public function processFile(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can view movements.
     */
    public function viewMovements(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }
}
