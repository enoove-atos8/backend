<?php

namespace App\Domain\Auth\Actions;

use App\Domain\Accounts\Users\Constants\ReturnMessages;
use App\Domain\Accounts\Users\Interfaces\UserRepositoryInterface;
use App\Domain\Auth\DataTransferObjects\ChangePasswordData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Infrastructure\Exceptions\GeneralExceptions;

class ChangePasswordAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    /**
     * @param ChangePasswordData $changePasswordData
     * @return bool
     * @throws GeneralExceptions
     */
    public function execute(ChangePasswordData $changePasswordData): bool
    {
        $user = Auth::user();

        if (!$user) {
            throw new GeneralExceptions(ReturnMessages::ERROR_UNAUTHORIZED, 401);
        }

        if (!Hash::check($changePasswordData->currentPassword, $user->password)) {
            throw new GeneralExceptions(ReturnMessages::ERROR_INCORRECT_CURRENT_PASSWORD, 400);
        }

        return $this->userRepository->changePassword(
            $user->id,
            $changePasswordData->newPassword
        );
    }
}
