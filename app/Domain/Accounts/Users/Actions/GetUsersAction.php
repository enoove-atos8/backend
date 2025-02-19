<?php

namespace App\Domain\Accounts\Users\Actions;


use App\Domain\Accounts\Users\Interfaces\UserRepositoryInterface;
use App\Infrastructure\Repositories\Accounts\User\UserRepository;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class GetUsersAction
{
    private UserRepository $userRepository;

    public function __construct(UserRepositoryInterface $userRepositoryInterface)
    {
        $this->userRepository = $userRepositoryInterface;
    }



    /**
     * @throws Throwable
     */
    public function execute(): Collection
    {
        $user = $this->userRepository->getUsers();

        if(!is_object($user))
            throw new GeneralExceptions('Erro ao criar um usuário para o tenant', 500);

        if($user->count() == 0)
            throw new GeneralExceptions('Nenhum usuário encontrado!', 404);

        return $user;
    }
}
