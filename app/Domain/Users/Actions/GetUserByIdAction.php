<?php

namespace Domain\Users\Actions;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\User\MemberRepository;
use Domain\Users\DataTransferObjects\MemberData;
use Domain\Users\Interfaces\MemberRepositoryInterface;
use Domain\Users\Models\User;
use Throwable;

class GetUserByIdAction
{
    private MemberRepository $userRepository;

    public function __construct(MemberRepositoryInterface $userRepositoryInterface)
    {
        $this->userRepository = $userRepositoryInterface;
    }


    /**
     * @param null $id
     * @return Model
     * @throws GeneralExceptions
     * @throws BindingResolutionException
     */
    public function __invoke($id = null): Model
    {
        $user = $this->userRepository->getUsers($id);

        if(!is_object($user))
            throw new GeneralExceptions('Erro ao retornar este usuário', 500);

        if($user->count() == 0)
            throw new GeneralExceptions('Nenhum usuário encontrado!', 404);

        return $user;
    }
}
