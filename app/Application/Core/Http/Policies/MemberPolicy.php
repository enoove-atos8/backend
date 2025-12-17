<?php

declare(strict_types=1);

namespace Application\Core\Http\Policies;

use App\Domain\Accounts\Users\Models\User;
use Domain\Secretary\Membership\Models\Member;
use Illuminate\Auth\Access\HandlesAuthorization;

class MemberPolicy
{
    use HandlesAuthorization;

    /**
     * Roles que têm acesso ao módulo de membros.
     */
    private const ALLOWED_ROLES = ['admin', 'pastor', 'secretary'];

    /**
     * Roles que podem visualizar dizimistas (inclui treasury).
     */
    private const TITHERS_ROLES = ['admin', 'pastor', 'secretary', 'treasury'];

    /**
     * Determine whether the user can view any members.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can view the member.
     */
    public function view(User $user, Member $member): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can create members.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can update the member.
     */
    public function update(User $user, Member $member): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can update member status.
     */
    public function updateStatus(User $user, Member $member): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can delete the member.
     */
    public function delete(User $user, Member $member): bool
    {
        return $user->hasAnyRole(['admin', 'pastor']);
    }

    /**
     * Determine whether the user can view birthdays.
     */
    public function viewBirthdays(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can view tithers.
     */
    public function viewTithers(User $user): bool
    {
        return $user->hasAnyRole(self::TITHERS_ROLES);
    }

    /**
     * Determine whether the user can view indicators.
     */
    public function viewIndicators(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can upload avatar.
     */
    public function uploadAvatar(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }

    /**
     * Determine whether the user can batch create members.
     */
    public function batchCreate(User $user): bool
    {
        return $user->hasAnyRole(self::ALLOWED_ROLES);
    }
}
