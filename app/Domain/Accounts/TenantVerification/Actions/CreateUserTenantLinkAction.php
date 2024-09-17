<?php

namespace App\Domain\Accounts\TenantVerification\Actions;

use App\Domain\Accounts\Users\DataTransferObjects\UserData;
use App\Domain\Accounts\Users\DataTransferObjects\UserDetailData;
use App\Domain\Accounts\Users\Interfaces\UserRepositoryInterface;
use App\Domain\Accounts\Users\Models\User;
use App\Domain\Notifications\Actions\Church\NewChurchUserNotificationAction;
use App\Domain\Notifications\Actions\User\NewUserNotificationAction;
use App\Infrastructure\Repositories\Accounts\User\UserRepository;
use Exception;
use Throwable;

class CreateUserTenantLinkAction
{


    public function __construct(
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function __invoke(string $email, string $tenant)
    {

    }
}
