<?php

namespace App\Domain\Accounts\Users\Actions;


use App\Domain\Accounts\Users\Interfaces\UserRepositoryInterface;
use App\Infrastructure\Repositories\Accounts\User\UserRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Exceptions\GeneralExceptions;

class GetUserByIdAction
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepositoryInterface)
    {
        $this->userRepository = $userRepositoryInterface;
    }


    /**
     * @param null $id
     * @return Model
     * @throws GeneralExceptions
     * @throws BindingResolutionException
     */
    public function execute($id = null): Model
    {
        $user = $this->userRepository->getUsers($id);

        if(!is_object($user))
            throw new GeneralExceptions('Erro ao retornar este usuário', 500);

        if($user->count() == 0)
            throw new GeneralExceptions('Nenhum usuário encontrado!', 404);

        return $user;
    }
}
