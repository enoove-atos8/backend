<?php

namespace Domain\Users\Actions;

use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\User\UserRepository;
use Domain\Users\DataTransferObjects\UserData;
use Domain\Users\Interfaces\UserRepositoryInterface;
use Domain\Users\Models\User;
use Throwable;

class GetUserByIdAction
{
    private UserRepository $userRepository;

    public function __construct(UserRepositoryInterface $userRepositoryInterface)
    {
        $this->userRepository = $userRepositoryInterface;
    }



    /**
     * @throws Throwable
     */
    public function __invoke($id = null): Collection
    {
        $user = $this->userRepository->getUsers($id);

        if(!is_object($user))
            throw new GeneralExceptions('Erro ao retornar este usuário', 500);

        if($user->count() == 0)
            throw new GeneralExceptions('Nenhum usuário encontrado!', 404);

        return $user;
    }
}
