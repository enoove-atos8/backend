<?php

declare(strict_types=1);

namespace Application\Core\Http\Policies;

use App\Domain\Accounts\Users\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BillingPolicy
{
    use HandlesAuthorization;

    /**
     * Roles que podem visualizar informações de billing.
     */
    private const VIEW_ROLES = ['admin', 'pastor'];

    /**
     * Roles que podem gerenciar billing.
     */
    private const MANAGEMENT_ROLES = ['admin'];

    /**
     * Determine whether the user can view billing plans.
     */
    public function viewPlans(User $user): bool
    {
        return $user->hasAnyRole(self::VIEW_ROLES);
    }

    /**
     * Determine whether the user can view subscription details.
     */
    public function viewSubscription(User $user): bool
    {
        return $user->hasAnyRole(self::VIEW_ROLES);
    }

    /**
     * Determine whether the user can view payment methods.
     */
    public function viewPaymentMethods(User $user): bool
    {
        return $user->hasAnyRole(self::VIEW_ROLES);
    }

    /**
     * Determine whether the user can add payment methods.
     */
    public function addPaymentMethod(User $user): bool
    {
        return $user->hasAnyRole(self::MANAGEMENT_ROLES);
    }

    /**
     * Determine whether the user can set default payment method.
     */
    public function setDefaultPaymentMethod(User $user): bool
    {
        return $user->hasAnyRole(self::MANAGEMENT_ROLES);
    }

    /**
     * Determine whether the user can delete payment methods.
     */
    public function deletePaymentMethod(User $user): bool
    {
        return $user->hasAnyRole(self::MANAGEMENT_ROLES);
    }

    /**
     * Determine whether the user can view invoices.
     */
    public function viewInvoices(User $user): bool
    {
        return $user->hasAnyRole(self::VIEW_ROLES);
    }

    /**
     * Determine whether the user can pay invoices.
     */
    public function payInvoice(User $user): bool
    {
        return $user->hasAnyRole(self::MANAGEMENT_ROLES);
    }
}
