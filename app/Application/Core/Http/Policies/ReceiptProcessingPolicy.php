<?php

declare(strict_types=1);

namespace Application\Core\Http\Policies;

use App\Domain\Accounts\Users\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReceiptProcessingPolicy
{
    use HandlesAuthorization;

    /**
     * Roles que têm acesso ao processamento de recibos.
     */
    private const ALLOWED_ROLES = ['admin', 'pastor', 'treasury'];

    /**
     * Determine whether the user can view receipts processing.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can delete receipts processing.
     */
    public function delete(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can view duplicities.
     */
    public function viewDuplicities(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can save duplicity analysis.
     */
    public function saveDuplicityAnalysis(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }
}
