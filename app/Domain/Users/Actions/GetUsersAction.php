<?php

namespace Domain\Users\Actions;

use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\User\MemberRepository;
use Domain\Users\DataTransferObjects\MemberData;
use Domain\Users\Interfaces\MemberRepositoryInterface;
use Domain\Users\Models\User;
use Throwable;

class GetUsersAction
{
    private MemberRepository $userRepository;

    public function __construct(MemberRepositoryInterface $userRepositoryInterface)
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
