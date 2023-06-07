<?php

namespace Domain\Users\Actions;

use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\User\UserRepository;
use Domain\Users\DataTransferObjects\UserData;
use Domain\Users\Interfaces\UserRepositoryInterface;
use Domain\Users\Models\User;

class CreateUserAction
{
    private UserRepository $userRepository;

    public function __construct(UserRepositoryInterface $userRepositoryInterface)
    {
        $this->userRepository = $userRepositoryInterface;
    }

    /**
     * @throws \Throwable
     */
    public function __invoke(UserData $userData): User
    {
        $user = $this->userRepository->createUser($userData);
        throw_if(!is_object($user), GeneralExceptions::class, 'Erro ao criar um usuário para o tenant', 500);

        return $user;
    }
}
