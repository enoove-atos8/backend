<?php

namespace App\Domain\Accounts\Users\Actions;

use App\Domain\Accounts\Users\DataTransferObjects\UserData;
use App\Domain\Accounts\Users\DataTransferObjects\UserDetailData;
use App\Domain\Accounts\Users\Interfaces\UserRepositoryInterface;
use App\Domain\Accounts\Users\Models\User;
use App\Domain\Notifications\Actions\Church\NewChurchUserNotificationAction;
use App\Domain\Notifications\Actions\User\NewUserNotificationAction;
use App\Infrastructure\Repositories\Accounts\User\UserRepository;
use Throwable;

class CreateUserAction
{
    private UserRepository $userRepository;
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
    public function __invoke(UserData $userData, UserDetailData $userDetailData, string $tenant, bool $firstUserChurch = false): User
    {
        if(!$firstUserChurch)
            $userData->password = $this->generatePassword();

        $user = $this->userRepository->createUser($userData);
        $this->createUserDetailAction->__invoke($user->id, $userDetailData);

        $user->assignRole($userData->roles);

        if($firstUserChurch)
            $this->newChurchUserNotificationAction->__invoke($userData, $userDetailData, $tenant);
        else
            $this->newUserNotificationAction->__invoke($userData, $userDetailData, $tenant);

        return $user;
    }


    /**
     * Generate password
     */
    public function generatePassword(): string
    {
        $digits    = array_flip(range('0', '9'));
        $combined  = array_merge($digits);

        return str_shuffle(array_rand($digits) .
            implode(array_rand($combined, rand(4, 7))));
    }
}
