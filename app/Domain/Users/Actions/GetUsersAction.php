<?php

namespace Domain\Users\Actions;

use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\User\UserRepository;
use Domain\Users\DataTransferObjects\UserData;
use Domain\Users\Interfaces\UserRepositoryInterface;
use Domain\Users\Models\User;
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
    public function __invoke(): Collection
    {
        $user = $this->userRepository->getUsers();

        if(!is_object($user))
            throw new GeneralExceptions('Erro ao criar um usuário para o tenant', 500);

        if($user->count() == 0)
            throw new GeneralExceptions('Nenhum usuário encontrado!', 404);

        return $user;
    }
}
