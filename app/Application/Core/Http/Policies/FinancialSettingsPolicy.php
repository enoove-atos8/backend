<?php

declare(strict_types=1);

namespace Application\Core\Http\Policies;

use App\Domain\Accounts\Users\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FinancialSettingsPolicy
{
    use HandlesAuthorization;

    /**
     * Roles que podem visualizar configurações financeiras.
     */
    private const VIEW_ROLES = ['admin', 'pastor', 'treasury'];

    /**
     * Roles que podem gerenciar configurações financeiras.
     */
    private const MANAGEMENT_ROLES = ['admin', 'pastor'];

    /**
     * Determine whether the user can view financial settings.
     */
    public function view(User $user): bool
    {
        return $user->hasAnyRole(self::VIEW_ROLES);
    }

    /**
     * Determine whether the user can update financial settings.
     */
    public function update(User $user): bool
    {
        return $user->hasAnyRole(self::MANAGEMENT_ROLES);
    }
}
