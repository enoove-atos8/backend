<?php

declare(strict_types=1);

namespace Application\Core\Http\Policies;

use App\Domain\Accounts\Users\Models\User;
use Domain\Financial\AccountsAndCards\Cards\Models\Card;
use Illuminate\Auth\Access\HandlesAuthorization;

class CardPolicy
{
    use HandlesAuthorization;

    /**
     * Roles que têm acesso ao módulo de cartões.
     */
    private const ALLOWED_ROLES = ['admin', 'pastor', 'treasury'];

    /**
     * Roles que podem desativar cartões.
     */
    private const DEACTIVATE_ROLES = ['admin', 'pastor'];

    /**
     * Determine whether the user can view any cards.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can view the card.
     */
    public function view(User $user, Card $card): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can create cards.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can update the card.
     */
    public function update(User $user, Card $card): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can deactivate the card.
     */
    public function deactivate(User $user, Card $card): bool
    {
        return $user->hasAnyRole(self::DEACTIVATE_ROLES);
    }

    /**
     * Determine whether the user can delete the card.
     */
    public function delete(User $user, Card $card): bool
    {
        // Apenas admin pode deletar permanentemente
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view invoices.
     */
    public function viewInvoices(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can view installments.
     */
    public function viewInstallments(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }
}
