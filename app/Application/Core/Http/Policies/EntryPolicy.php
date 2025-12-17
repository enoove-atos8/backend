<?php

declare(strict_types=1);

namespace Application\Core\Http\Policies;

use App\Domain\Accounts\Users\Models\User;
use App\Domain\Financial\Entries\Entries\Models\Entry;
use Illuminate\Auth\Access\HandlesAuthorization;

class EntryPolicy
{
    use HandlesAuthorization;

    /**
     * Roles que têm acesso ao módulo financeiro de entradas.
     */
    private const ALLOWED_ROLES = ['admin', 'pastor', 'treasury'];

    /**
     * Determine whether the user can view any entries.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can view the entry.
     */
    public function view(User $user, Entry $entry): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can create entries.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can update the entry.
     */
    public function update(User $user, Entry $entry): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can delete the entry.
     */
    public function delete(User $user, Entry $entry): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can upload receipts.
     */
    public function uploadReceipt(User $user): bool
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
