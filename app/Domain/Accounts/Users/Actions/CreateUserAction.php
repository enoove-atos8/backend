<?php

namespace App\Domain\Accounts\Users\Actions;

use App\Domain\Accounts\Users\DataTransferObjects\UserData;
use App\Domain\Accounts\Users\DataTransferObjects\UserDetailData;
use App\Domain\Accounts\Users\Interfaces\UserRepositoryInterface;
use App\Domain\Accounts\Users\Models\User;
use App\Domain\Notifications\Actions\Church\NewChurchUserNotificationAction;
use App\Domain\Notifications\Actions\User\NewUserNotificationAction;
use App\Infrastructure\Repositories\Users\User\UserRepository;
use Exception;
use Throwable;

class CreateUserAction
{
    private UserRepositoryInterface $userRepository;
    private CreateUserDetailAction $createUserDetailAction;
    private NewUserNotificationAction $newUserNotificationAction;
    private NewChurchUserNotificationAction $newChurchUserNotificationAction;

    public function __construct(
        UserRepositoryInterface $userRepositoryInterface,
        CreateUserDetailAction $createUserDetailAction,
        NewUserNotificationAction $newUserNotificationAction,
        NewChurchUserNotificationAction $newChurchUserNotificationAction,
    )
    {
        $this->userRepository = $userRepositoryInterface;
        $this->createUserDetailAction = $createUserDetailAction;
        $this->newUserNotificationAction = $newUserNotificationAction;
        $this->newChurchUserNotificationAction = $newChurchUserNotificationAction;
    }

    /**
     * @throws Throwable
     */
    public function execute(UserData $userData, UserDetailData $userDetailData, string $tenant, bool $firstUserChurch = false): User
    {
        if(!$firstUserChurch)
            $userData->password = $this->generatePassword();

        $user = $this->userRepository->createUser($userData);
        $this->createUserDetailAction->execute($user->id, $userDetailData);

        $user->assignRole($userData->roles);

        if($firstUserChurch)
            $this->newChurchUserNotificationAction->execute($userData, $userDetailData, $tenant);
        else
            $this->newUserNotificationAction->execute($userData, $userDetailData, $tenant);

        return $user;
    }


    /**
     * Generate password
     * @throws Exception
     */
    public function generatePassword($length = 6): string
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++)
            $randomString .= $characters[random_int(0, $charactersLength - 1)];

        return $randomString;
    }
}
